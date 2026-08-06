<?php

namespace Modules\LayananImagingOrder\Services;

use Illuminate\Support\Facades\DB;
use Modules\LayananImagingOrder\Models\ImagingOrder;
use Modules\LayananImagingOrder\Models\ImagingStudy;

/**
 * Pencatatan studi imaging. Tidak ada endpoint "complete" terpisah: pencatatan
 * studi ADALAH bukti pengerjaan, dan transisi order → completed terjadi hanya
 * di dalam transaksi yang sama dengan studi tercipta — status tidak pernah
 * bisa lepas dari bukti pemeriksaannya.
 */
class ImagingStudyService
{
    /**
     * Rekam studi hasil pengerjaan order.
     *
     * @param  array<string, mixed>  $data  hasil validasi StoreImagingStudyRequest
     */
    public function record(array $data): ImagingStudy
    {
        return DB::transaction(function () use ($data) {
            // lockForUpdate: dua operator yang merekam studi untuk order sama
            // secara bersamaan tetap menghasilkan transisi status konsisten.
            $order = ImagingOrder::query()->lockForUpdate()->findOrFail($data['imaging_order_id']);

            abort_if(
                $order->status === ImagingOrder::STATUS_CANCELLED,
                422,
                "Order imaging #{$order->id} sudah dibatalkan; studi tidak dapat dicatat.",
            );

            $study = ImagingStudy::create($data);

            // Order completed/scheduled boleh menambah studi lagi (satu order
            // bisa melahirkan lebih dari satu seri/studi di PACS nyata), tetapi
            // hanya order yang belum selesai yang ikut berubah status.
            if (in_array($order->status, [ImagingOrder::STATUS_ORDERED, ImagingOrder::STATUS_SCHEDULED], true)) {
                $order->update(['status' => ImagingOrder::STATUS_COMPLETED]);
            }

            return $study;
        });
    }

    /**
     * Amendemen hasil (findings_summary/report_url/performed_at/study_instance_uid).
     * Ditolak bila ordernya sudah dibatalkan: dokumen hasil di atas order batal
     * tidak boleh terus diubah seolah pemeriksaan masih berjalan.
     *
     * @param  array<string, mixed>  $data  hasil validasi UpdateImagingStudyRequest
     */
    public function updateDetails(ImagingStudy $study, array $data): ImagingStudy
    {
        return DB::transaction(function () use ($study, $data) {
            abort_if(
                $study->order->status === ImagingOrder::STATUS_CANCELLED,
                422,
                "Studi #{$study->id} berada pada order yang sudah dibatalkan; tidak dapat disunting.",
            );

            $study->update($data);

            return $study->refresh();
        });
    }
}
