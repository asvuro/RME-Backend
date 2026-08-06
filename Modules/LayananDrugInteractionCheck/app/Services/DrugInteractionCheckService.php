<?php

namespace Modules\LayananDrugInteractionCheck\Services;

use Illuminate\Support\Facades\DB;
use Modules\GeneralPatient\Models\Patient;
use Modules\LayananDrugInteractionCheck\Models\DrugInteractionRule;
use Modules\LayananPrescription\Models\Prescription;
use Modules\LayananPrescriptionItem\Models\PrescriptionItem;
use Modules\MedicalRecordAllergy\Models\Allergy;

/**
 * CDSS advisory interaksi obat & alergi - MURNI rule engine internal, tanpa
 * API farmakologi eksternal.
 *
 * Modul ini read-only terhadap alur resep/dispense: hasil cek TIDAK pernah
 * mengubah state Prescription maupun memblokir DispenseService. Temuan hanya
 * dikembalikan sebagai array pesan untuk ditampilkan ke petugas.
 */
class DrugInteractionCheckService
{
    /**
     * Jendela pandang resep sebelumnya pada visit yang sama: obat yang masih
     * "menginap" dari pelayanan <= 48 jam ikut diperiksa walaupun bukan bagian
     * resep yang sedang dicek (klinik: efek kumulatif/cross-therapy).
     */
    private const LOOKBACK_HOURS = 48;

    /**
     * Bobot urutan tampilan temuan (terberat dulu). Interaction severity dan
     * allergy severity dipetakan ke satu skala agar daftar bisa disort flat.
     */
    private const SEVERITY_WEIGHTS = [
        DrugInteractionRule::SEVERITY_MINOR => 1,
        'mild' => 1,
        DrugInteractionRule::SEVERITY_MODERATE => 2,
        'moderate' => 2,
        DrugInteractionRule::SEVERITY_MAJOR_CONTRAINDICATED => 3,
        'severe' => 3,
    ];

    /**
     * Cek interaksi + alergi untuk satu resep.
     *
     * @return array<int, array{type: string, severity: string|null, message: string}>
     */
    public function checkPrescription(int $prescriptionId): array
    {
        /** @var Prescription $prescription */
        $prescription = Prescription::query()->findOrFail($prescriptionId);

        // Kandidat interaksi = item resep ini + item resep lain milik visit
        // YANG SAMA dalam jendela 48 jam, statusnya masih hidup (active/dispensed;
        // cancelled tidak dihitung karena obatnya batal dilayani).
        $currentItems = $prescription->items()->with('item')->get();

        $lookbackItems = PrescriptionItem::query()
            ->with('item')
            ->whereHas('prescription', function ($query) use ($prescription): void {
                $query->where('visit_id', $prescription->visit_id)
                    ->whereKeyNot($prescription->getKey())
                    ->whereIn('status', ['active', 'dispensed'])
                    ->where('prescribed_at', '>=', now()->subHours(self::LOOKBACK_HOURS));
            })
            ->get();

        // Label id -> nama obat (fallback drug_name bila baris item tak punya FK item,
        // mis. entri manual tanpa master) - dipakai untuk menyusun pesan temuan.
        $labels = [];
        foreach ($currentItems->concat($lookbackItems) as $row) {
            if ($row->item_id !== null) {
                $labels[$row->item_id] = $row->item?->name ?? $row->drug_name;
            }
        }

        // Hanya baris dengan item_id bisa dicocokkan ke rule (rule berbasis FK master).
        $currentIds = array_values(array_unique(
            $currentItems->pluck('item_id')->filter()->map(fn ($id) => (int) $id)->all(),
        ));
        $poolIds = array_values(array_unique(array_merge(
            $currentIds,
            $lookbackItems->pluck('item_id')->filter()->map(fn ($id) => (int) $id)->all(),
        )));

        $findings = [];

        foreach ($this->matchInteractions($currentIds, $poolIds) as [$aId, $bId, $rule]) {
            $lintasResep = ! in_array($aId, $currentIds, true) || ! in_array($bId, $currentIds, true);
            $findings[] = [
                'type' => 'interaction',
                'severity' => $rule->severity,
                'message' => sprintf(
                    'Interaksi %s: %s + %s. %s%s',
                    $rule->severity,
                    $labels[$aId],
                    $labels[$bId],
                    $rule->clinical_note,
                    $lintasResep ? ' (pasangan melibatkan obat dari resep lain kunjungan ini dalam '.$this->lookbackLabel().')' : '',
                ),
            ];
        }

        // Alergi hanya dinilai terhadap obat yang DIRESEPKAN SEKARANG - alergi
        // pasien relevan saat penulisan resep, bukan retroaktif ke resep lama.
        $patient = $prescription->visit?->registration?->patient;
        $allergies = $patient === null ? collect() : Allergy::query()
            ->where('patient_id', $patient->getKey())
            ->where('is_active', true)
            ->get();

        foreach ($currentItems as $row) {
            $drugLabel = $row->item?->name ?? $row->drug_name;

            foreach ($allergies as $allergy) {
                if (! $this->allergenMatches((string) $allergy->allergen, (string) $drugLabel)) {
                    continue;
                }

                // Severity mengikuti catatan alergi (mild/moderate/severe), boleh null
                // karena kolom allergies.severity nullable di modul MedicalRecordAllergy.
                $findings[] = [
                    'type' => 'allergy',
                    'severity' => $allergy->severity,
                    'message' => sprintf(
                        'Alergi pasien: %s (%s) - cocok dengan obat diresep %s.%s',
                        $allergy->allergen,
                        $allergy->category,
                        $drugLabel,
                        filled($allergy->reaction) ? ' Reaksi: '.$allergy->reaction.'.' : '',
                    ),
                ];
            }
        }

        usort($findings, fn (array $x, array $y): int => $this->weightOf($y['severity']) <=> $this->weightOf($x['severity']));

        return $findings;
    }

    public function storeRule(array $data): DrugInteractionRule
    {
        $a = (int) $data['item_id_a'];
        $b = (int) $data['item_id_b'];

        abort_if($a === $b, 422, 'Pasangan interaksi tidak boleh berisi obat yang sama.');

        return DB::transaction(function () use ($a, $b, $data): DrugInteractionRule {
            abort_if($this->pairExists($a, $b), 422, 'Aturan interaksi untuk pasangan obat ini sudah terdaftar.');

            return DrugInteractionRule::create($data);
        });
    }

    public function updateRule(int $ruleId, array $data): DrugInteractionRule
    {
        return DB::transaction(function () use ($ruleId, $data): DrugInteractionRule {
            /** @var DrugInteractionRule $rule */
            $rule = DrugInteractionRule::query()->lockForUpdate()->findOrFail($ruleId);

            $a = (int) ($data['item_id_a'] ?? $rule->item_id_a);
            $b = (int) ($data['item_id_b'] ?? $rule->item_id_b);

            abort_if($a === $b, 422, 'Pasangan interaksi tidak boleh berisi obat yang sama.');

            // Gerbang duplikat hanya relevan bila pasangannya benar-benar berganti;
            // edit severity/catatan pada pasangan yang sama tetap boleh.
            $pairChanged = $a !== (int) $rule->item_id_a || $b !== (int) $rule->item_id_b;

            abort_if($pairChanged && $this->pairExists($a, $b), 422, 'Aturan interaksi untuk pasangan obat ini sudah terdaftar.');

            $rule->update($data);

            return $rule->refresh();
        });
    }

    public function deleteRule(int $ruleId): void
    {
        DB::transaction(function () use ($ruleId): void {
            /** @var DrugInteractionRule $rule */
            $rule = DrugInteractionRule::query()->lockForUpdate()->findOrFail($ruleId);
            $rule->delete();
        });
    }

    /**
     * Cocokkan setiap aturan ke pool item. Pasangan dianggap kena bila minimal
     * SATU ujungnya ada di resep yang sedang dicek dan kedua ujungnya ada di
     * pool (resep ini + lookback). Rule dibaca dua arah (A-B dan B-A) sehingga
     * arah penyimpanan rule tidak pengaruhi hasil.
     *
     * @param int[] $currentIds
     * @param int[] $poolIds
     * @return array<int, array{0: int, 1: int, 2: DrugInteractionRule}>
     */
    private function matchInteractions(array $currentIds, array $poolIds): array
    {
        if ($currentIds === [] || count($poolIds) < 2) {
            return [];
        }

        $rules = DrugInteractionRule::query()
            ->where(function ($query) use ($poolIds): void {
                $query->whereIn('item_id_a', $poolIds)->orWhereIn('item_id_b', $poolIds);
            })
            ->get();

        $matched = [];

        foreach ($rules as $rule) {
            $a = (int) $rule->item_id_a;
            $b = (int) $rule->item_id_b;

            if (! in_array($a, $poolIds, true) || ! in_array($b, $poolIds, true)) {
                continue; // salah satu ujung rule tidak muncul di resep/pool mana pun
            }

            if (! in_array($a, $currentIds, true) && ! in_array($b, $currentIds, true)) {
                continue; // kedua ujung hanya dari resep lama - bukan masalah resep ini
            }

            $key = min($a, $b).':'.max($a, $b);

            // Dedupe defensif: gerbang store/update menjamin unordered pair unik,
            // tapi jaga bila data historis berisi duplikat terbalik.
            $matched[$key] ??= [$a, $b, $rule];
        }

        return array_values($matched);
    }

    /** Pasangan unordered sudah terdaftar (A-B maupun B-A)? */
    private function pairExists(int $a, int $b): bool
    {
        return DrugInteractionRule::query()
            ->where(function ($query) use ($a, $b): void {
                $query->where(function ($q) use ($a, $b): void {
                    $q->where('item_id_a', $a)->where('item_id_b', $b);
                })->orWhere(function ($q) use ($a, $b): void {
                    $q->where('item_id_a', $b)->where('item_id_b', $a);
                });
            })
            ->exists();
    }

    /**
     * Pencocokan alergi berbasis teks dua arah: "Amoxicillin" kena bila pasien
     * alergen "amoxicillin" maupun "amoxicillin 500 kapsul". Asumsi desain:
     * model Allergy (MedicalRecordAllergy) menyimpan allergen sebagai teks bebas
     * tanpa FK ke Item/ActiveIngredient, sehingga pencocokan substring case-
     * insensitive adalah pendekatan tertinggi yang bisa dilakukan lintas modul;
     * alergi level kelas obat (mis. "penisilin" vs amoxicillin) belum tertangkap
     * sampai ada mapping item -> active ingredient.
     */
    private function allergenMatches(string $allergen, string $drugLabel): bool
    {
        $allergen = mb_strtolower(trim($allergen));
        $label = mb_strtolower(trim($drugLabel));

        return $allergen !== '' && $label !== ''
            && (str_contains($label, $allergen) || str_contains($allergen, $label));
    }

    private function weightOf(?string $severity): int
    {
        return self::SEVERITY_WEIGHTS[$severity] ?? 0;
    }

    private function lookbackLabel(): string
    {
        return self::LOOKBACK_HOURS.' jam terakhir';
    }
}
