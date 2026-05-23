<?php

namespace Modules\Sitb\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Sitb\Models\PasienTb;
use Modules\Sitb\Services\SitbService;

class SitbController extends Controller
{
    /**
     * Columns owned by the pipeline, never accepted from the request:
     * kirim/oleh are stamped by the controller, final/error_message/id_tb_03
     * reflect the SITB exchange itself (service-assigned). They have no
     * validation rules and are stripped again via safe()->except() below as
     * defence in depth.
     */
    private const INTERNAL_COLUMNS = ['kirim', 'oleh', 'final', 'error_message', 'id_tb_03'];

    public function __construct(private readonly SitbService $service)
    {
    }

    public function index(Request $request)
    {
        $query = PasienTb::query();
        if ($request->filled('kirim')) {
            $query->where('kirim', $request->integer('kirim'));
        }

        return $query->latest()->paginate($request->integer('per_page', 15));
    }

    public function show(PasienTb $pasienTb)
    {
        return $pasienTb;
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        return response()->json($this->service->kirim(
            PasienTb::create($validated + ['kirim' => 1, 'oleh' => auth()->id()])
        ))->setStatusCode(201);
    }

    public function update(Request $request, PasienTb $pasienTb)
    {
        // Source: any update re-queues the row for resend (kirim = 1).
        $pasienTb->fill($this->validated($request));
        $pasienTb->kirim = 1;
        $pasienTb->save();

        return response()->json($this->service->kirim($pasienTb));
    }

    /**
     * Explicit per-column rules mirroring the pasien_tb migration types
     * (every column is nullable there, hence nullable everywhere). Returns
     * only the whitelisted columns - internal ones can never pass through.
     */
    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'nourut_pasien' => ['nullable', 'string', 'max:255'],
            'id_periode_laporan' => ['nullable', 'string', 'max:255'],
            'tanggal_buat_laporan' => ['nullable', 'date'],
            'tahun_buat_laporan' => ['nullable', 'string', 'max:255'],
            'kd_wasor' => ['nullable', 'string', 'max:255'],
            'noregkab' => ['nullable', 'string', 'max:255'],
            'kd_pasien' => ['nullable', 'string', 'max:255'],
            'nik' => ['nullable', 'string', 'max:255'],
            'jenis_kelamin' => ['nullable', 'integer', 'in:1,2'], // 1=L, 2=P
            'alamat_lengkap' => ['nullable', 'string'],
            'id_propinsi_faskes' => ['nullable', 'string', 'max:255'],
            'kd_kabupaten_faskes' => ['nullable', 'string', 'max:255'],
            'id_propinsi_pasien' => ['nullable', 'string', 'max:255'],
            'kd_kabupaten_pasien' => ['nullable', 'string', 'max:255'],
            'id_kecamatan_pasien' => ['nullable', 'string', 'max:255'],
            'id_kelurahan' => ['nullable', 'string', 'max:255'],
            'kd_fasyankes' => ['nullable', 'string', 'max:255'],
            'nama_rujukan' => ['nullable', 'string', 'max:255'],
            'sebutkan1' => ['nullable', 'string', 'max:255'],
            'tipe_diagnosis' => ['nullable', 'string', 'max:255'],
            'klasifikasi_lokasi_anatomi' => ['nullable', 'integer'],
            'klasifikasi_riwayat_pengobatan' => ['nullable', 'integer'],
            'klasifikasi_status_hiv' => ['nullable', 'integer'],
            'total_skoring_anak' => ['nullable', 'integer'],
            'konfirmasiSkoring5' => ['nullable', 'integer'],
            'konfirmasiSkoring6' => ['nullable', 'integer'],
            'tanggal_mulai_pengobatan' => ['nullable', 'date'],
            'paduan_oat' => ['nullable', 'string', 'max:255'],
            'sumber_obat' => ['nullable', 'integer'],
            'sebutkan' => ['nullable', 'string', 'max:255'],
            'sebelum_pengobatan_hasil_mikroskopis' => ['nullable', 'integer'],
            'sebelum_pengobatan_hasil_tes_cepat' => ['nullable', 'integer'],
            'sebelum_pengobatan_hasil_biakan' => ['nullable', 'integer'],
            'noreglab_bulan_2' => ['nullable', 'string', 'max:255'],
            'hasil_mikroskopis_bulan_2' => ['nullable', 'integer'],
            'noreglab_bulan_3' => ['nullable', 'string', 'max:255'],
            'hasil_mikroskopis_bulan_3' => ['nullable', 'integer'],
            'noreglab_bulan_5' => ['nullable', 'string', 'max:255'],
            'hasil_mikroskopis_bulan_5' => ['nullable', 'integer'],
            'akhir_pengobatan_noreglab' => ['nullable', 'string', 'max:255'],
            'akhir_pengobatan_hasil_mikroskopis' => ['nullable', 'integer'],
            'tanggal_hasil_akhir_pengobatan' => ['nullable', 'date'],
            'hasil_akhir_pengobatan' => ['nullable', 'integer'],
            'tanggal_dianjurkan_tes' => ['nullable', 'date'],
            'tanggal_tes_hiv' => ['nullable', 'date'],
            'hasil_tes_hiv' => ['nullable', 'integer'],
            'ppk' => ['nullable', 'integer'],
            'art' => ['nullable', 'integer'],
            'tb_dm' => ['nullable', 'integer'],
            'terapi_dm' => ['nullable', 'integer'],
            'pindah_ro' => ['nullable', 'integer'],
            'umur' => ['nullable', 'integer'],
            'status_pengobatan' => ['nullable', 'integer'],
            'foto_toraks' => ['nullable', 'integer'],
            'toraks_tdk_dilakukan' => ['nullable', 'integer'],
            'keterangan' => ['nullable', 'string'],
            'tahun' => ['nullable', 'string', 'max:255'],
            'no_bpjs' => ['nullable', 'string', 'max:255'],
            'tgl_lahir' => ['nullable', 'date'],
            'kode_icd_x' => ['nullable', 'string', 'max:255'],
            'asal_poli' => ['nullable', 'string', 'max:255'],
        ]);

        return collect($validated)->except(self::INTERNAL_COLUMNS)->all();
    }
}
