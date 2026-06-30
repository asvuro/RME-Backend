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
        $employee = Employee::query()->where('user_id', $userId)->first();

        if ($employee === null) {
            return [];
        }

        $wardIds = [];

        $staffMemberId = StaffMember::query()->where('employee_id', $employee->id)->value('id');
        if ($staffMemberId !== null) {
            $wardIds[] = StaffWardAssignment::query()
                ->where('staff_member_id', $staffMemberId)
                ->pluck('ward_id')->all();
        }

        $doctorId = Doctor::query()->where('employee_id', $employee->id)->value('id');
        if ($doctorId !== null) {
            $wardIds[] = DoctorWardAssignment::query()
                ->where('doctor_id', $doctorId)
                ->pluck('ward_id')->all();
        }

        $nurseId = Nurse::query()->where('employee_id', $employee->id)->value('id');
        if ($nurseId !== null) {
            $wardIds[] = NurseWardAssignment::query()
                ->where('nurse_id', $nurseId)
                ->pluck('ward_id')->all();
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
}
