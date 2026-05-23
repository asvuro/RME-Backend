<?php

namespace Modules\KemkesReport\Tests\Feature;

use Modules\Auth\Models\User;
use Modules\GeneralBed\Models\Bed;
use Modules\GeneralGender\Models\Gender;
use Modules\GeneralPatient\Models\Patient;
use Modules\GeneralRoom\Models\Room;
use Modules\GeneralRoomClass\Models\RoomClass;
use Modules\GeneralWard\Models\Ward;
use Modules\PendaftaranRegistration\Models\Registration;
use Modules\PendaftaranVisit\Models\Visit;
use Tests\TestCase;

/**
 * Basis bersama tiga suite laporan: bangsal/kamar/bed, pasien ber-gender,
 * dan kunjungan rawat inap dengan waktu masuk/pulang eksak.
 */
abstract class KemkesReportTestCase extends TestCase
{
    protected User $user;

    protected Gender $male;

    protected Gender $female;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'sanctum');

        // Padan JENIS_KELAMIN simgos2 — dicocokkan lewat kode, bukan id.
        $this->male = Gender::query()->create(['name' => 'Laki-laki', 'code' => 'L']);
        $this->female = Gender::query()->create(['name' => 'Perempuan', 'code' => 'P']);
    }

    protected function makeRoom(Ward $ward, ?int $classId = null): Room
    {
        return Room::factory()->create(['ward_id' => $ward->id, 'class_id' => $classId]);
    }

    protected function makeBeds(Room $room, int $count): array
    {
        $beds = [];
        for ($i = 0; $i < $count; $i++) {
            $beds[] = Bed::factory()->create(['room_id' => $room->id]);
        }

        return $beds;
    }

    protected function makePatient(Gender $gender): Patient
    {
        return Patient::factory()->create(['gender_id' => $gender->id]);
    }

    /**
     * Kunjungan rawat inap dengan waktu eksak — dibuat langsung (bukan lewat
     * service admit) supaya laporan historis bisa diuji deterministik.
     */
    protected function makeInpatientVisit(
        Ward $ward,
        ?Bed $bed,
        string $admittedAt,
        ?string $dischargedAt = null,
        ?Patient $patient = null,
    ): Visit {
        $registration = Registration::factory()->create([
            'patient_id' => $patient?->id ?? $this->makePatient($this->male)->id,
        ]);

        return Visit::factory()->create([
            'registration_id' => $registration->id,
            'ward_id' => $ward->id,
            'bed_id' => $bed?->id,
            'admitted_at' => $admittedAt,
            'discharged_at' => $dischargedAt,
            'status' => $dischargedAt !== null ? 'discharged' : 'active',
        ]);
    }
}
