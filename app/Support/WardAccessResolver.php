<?php

namespace App\Support;

use App\Modules\Contracts\WardScope;
use Modules\Auth\Models\User;
use Modules\GeneralDoctor\Models\Doctor;
use Modules\GeneralDoctorWardAssignment\Models\DoctorWardAssignment;
use Modules\GeneralEmployee\Models\Employee;
use Modules\GeneralNurse\Models\Nurse;
use Modules\GeneralNurseWardAssignment\Models\NurseWardAssignment;
use Modules\GeneralStaffMember\Models\StaffMember;
use Modules\GeneralStaffWardAssignment\Models\StaffWardAssignment;

/**
 * Rantai: User <- Employee (user_id) <- StaffMember/Doctor/Nurse (employee_id)
 * <- *WardAssignment (staff_member_id/doctor_id/nurse_id). User tanpa
 * Employee (mis. akun sistem) dianggap tidak punya ward assignment apa pun.
 *
 * Sengaja TIDAK di-cache lintas-request: data assignment berubah relatif
 * sering (mutasi staf) dan query-nya ringan (beberapa baris per user).
 */
class WardAccessResolver implements WardScope
{
    public function assignedWardIds(int $userId): array
    {
        // employees.user_id TIDAK unique -- satu user bisa punya lebih dari
        // satu baris Employee (mis. profil dibuat ulang). Kumpulkan assignment
        // dari SEMUA employee milik user ini, jangan cuma yang pertama
        // ditemukan -- kalau tidak, assignment bisa "hilang" gara-gara
        // resolver kebetulan ambil baris Employee yang salah.
        $employeeIds = Employee::query()->where('user_id', $userId)->pluck('id');

        if ($employeeIds->isEmpty()) {
            return [];
        }

        $wardIds = [];

        $staffMemberIds = StaffMember::query()->whereIn('employee_id', $employeeIds)->pluck('id');
        if ($staffMemberIds->isNotEmpty()) {
            $wardIds[] = StaffWardAssignment::query()
                ->whereIn('staff_member_id', $staffMemberIds)
                ->pluck('ward_id')->all();
        }

        $doctorIds = Doctor::query()->whereIn('employee_id', $employeeIds)->pluck('id');
        if ($doctorIds->isNotEmpty()) {
            $wardIds[] = DoctorWardAssignment::query()
                ->whereIn('doctor_id', $doctorIds)
                ->pluck('ward_id')->all();
        }

        $nurseIds = Nurse::query()->whereIn('employee_id', $employeeIds)->pluck('id');
        if ($nurseIds->isNotEmpty()) {
            $wardIds[] = NurseWardAssignment::query()
                ->whereIn('nurse_id', $nurseIds)
                ->pluck('ward_id')->all();
        }

        if ($wardIds === []) {
            return [];
        }

        return array_values(array_unique(array_merge(...$wardIds)));
    }

    public function canAccessWard(User $user, ?int $wardId): bool
    {
        if ($wardId === null) {
            return true;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        $assigned = $this->assignedWardIds($user->id);

        // Rollout bertahap: user yang belum PERNAH di-assign ward apa pun
        // (mis. belum ditautkan ke Employee, atau memang staf non-klinis)
        // dianggap belum masuk cakupan scoping ini -- default akses penuh,
        // sama seperti perilaku sebelum #3. Begitu punya >=1 assignment,
        // baru dibatasi ke ward yang di-assign saja.
        if ($assigned === []) {
            return true;
        }

        return in_array($wardId, $assigned, true);
    }

    public function applyReadScope($query, string $column, User $user)
    {
        if ($user->hasRole('admin')) {
            return $query;
        }

        $assigned = $this->assignedWardIds($user->id);

        if ($assigned !== []) {
            return $query->whereIn($column, $assigned);
        }

        // Rollout bertahap: user tanpa assignment apa pun dianggap belum masuk
        // cakupan scoping -- lihat semua, identik dengan canAccessWard().
        return $query;
    }
}
