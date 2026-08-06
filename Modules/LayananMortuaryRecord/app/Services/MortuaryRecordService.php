<?php

namespace Modules\LayananMortuaryRecord\Services;

use Illuminate\Support\Facades\DB;
use Modules\LayananMortuaryRecord\Models\MortuaryRecord;

/**
 * Gerbang kamar jenazah. Status hanya berubah in_mortuary -> released lewat
 * release(); tidak ada jalur balik (released -> in_mortuary) karena begitu
 * jenazah diambil keluarga, tidak ada skenario bisnis yang membalikkannya.
 */
class MortuaryRecordService
{
    public function admit(array $data): MortuaryRecord
    {
        return DB::transaction(function () use ($data) {
            return MortuaryRecord::create([
                ...$data,
                'status' => MortuaryRecord::STATUS_IN_MORTUARY,
            ]);
        });
    }

    /**
     * Ubah data non-status (mis. catatan sebab kematian). Status tidak boleh
     * diubah lewat jalur ini — satu-satunya transisi status ada di release().
     */
    public function updateDetails(MortuaryRecord $record, array $data): MortuaryRecord
    {
        return DB::transaction(function () use ($record, $data) {
            $record->update($data);

            return $record;
        });
    }

    /**
     * Gerbang rilis jenazah (#2). Hanya boleh dari status in_mortuary, dan
     * released_at + released_to_name + released_by wajib terisi lengkap saat
     * status berubah — supaya tidak ada record "released" tanpa jejak siapa
     * yang mengambil.
     */
    public function release(MortuaryRecord $record, array $data): MortuaryRecord
    {
        return DB::transaction(function () use ($record, $data) {
            $locked = MortuaryRecord::query()->lockForUpdate()->findOrFail($record->id);

            abort_if(
                $locked->status !== MortuaryRecord::STATUS_IN_MORTUARY,
                422,
                'Jenazah sudah dirilis; tidak dapat dirilis ulang.',
            );

            $locked->update([
                'released_at' => $data['released_at'],
                'released_to_name' => $data['released_to_name'],
                'released_to_relationship' => $data['released_to_relationship'] ?? null,
                'released_by' => $data['released_by'],
                'status' => MortuaryRecord::STATUS_RELEASED,
            ]);

            return $locked;
        });
    }

    public function deleteRecord(MortuaryRecord $record): void
    {
        DB::transaction(function () use ($record) {
            abort_if(
                $record->status === MortuaryRecord::STATUS_RELEASED,
                422,
                'Record kamar jenazah yang sudah dirilis tidak dapat dihapus (jejak audit).',
            );

            $record->delete();
        });
    }
}
