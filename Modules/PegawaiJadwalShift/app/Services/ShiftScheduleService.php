<?php

namespace Modules\PegawaiJadwalShift\Services;

use Illuminate\Support\Facades\DB;
use Modules\PegawaiJadwalShift\Models\ShiftSchedule;

/**
 * Gerbang bisnis jadwal shift. Dua hal yang dijaga di sini (bukan cuma di
 * FormRequest) karena melibatkan query lintas baris:
 * 1) pemilik jadwal harus persis satu dari staff_member_id/employee_id —
 *    DB tidak bisa menegakkan XOR antar dua kolom nullable.
 * 2) tidak boleh ada jadwal duplikat pegawai yang sama pada tanggal + jenis
 *    shift yang sama — didukung juga oleh unique index di migration sebagai
 *    lapis kedua, tapi dicek dulu di sini supaya pesan errornya rapi (422,
 *    bukan 500 dari QueryException).
 */
class ShiftScheduleService
{
    public function createSchedule(array $data): ShiftSchedule
    {
        return DB::transaction(function () use ($data) {
            $this->assertSingleOwner($data);
            $this->assertTimeRangeValid($data);
            $this->assertNoDuplicate($data);

            return ShiftSchedule::create($data);
        });
    }

    public function updateSchedule(ShiftSchedule $shiftSchedule, array $data): ShiftSchedule
    {
        return DB::transaction(function () use ($shiftSchedule, $data) {
            $merged = array_merge($shiftSchedule->only([
                'staff_member_id', 'employee_id', 'ward_id', 'shift_type', 'shift_date', 'start_time', 'end_time', 'status',
            ]), $data);

            $this->assertSingleOwner($merged);
            $this->assertTimeRangeValid($merged);
            $this->assertNoDuplicate($merged, $shiftSchedule->id);

            $shiftSchedule->update($data);

            return $shiftSchedule->refresh();
        });
    }

    public function deleteSchedule(ShiftSchedule $shiftSchedule): void
    {
        $shiftSchedule->delete();
    }

    /**
     * Pemilik jadwal wajib persis satu: staff_member_id XOR employee_id.
     */
    protected function assertSingleOwner(array $data): void
    {
        $hasStaffMember = ! empty($data['staff_member_id']);
        $hasEmployee = ! empty($data['employee_id']);

        abort_if(
            $hasStaffMember === $hasEmployee,
            422,
            'Isi salah satu dari staff_member_id atau employee_id (tidak boleh keduanya atau tidak ada sama sekali).',
        );
    }

    protected function assertTimeRangeValid(array $data): void
    {
        abort_if(
            isset($data['start_time'], $data['end_time']) && $data['end_time'] <= $data['start_time'],
            422,
            'Jam selesai (end_time) harus setelah jam mulai (start_time).',
        );
    }

    protected function assertNoDuplicate(array $data, ?int $excludeId = null): void
    {
        $query = ShiftSchedule::query()
            ->where('shift_date', $data['shift_date'])
            ->where('shift_type', $data['shift_type']);

        if (! empty($data['staff_member_id'])) {
            $query->where('staff_member_id', $data['staff_member_id']);
        } else {
            $query->where('employee_id', $data['employee_id']);
        }

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        abort_if(
            $query->exists(),
            422,
            'Pegawai ini sudah punya jadwal shift pada tanggal dan jenis shift tersebut.',
        );
    }
}
