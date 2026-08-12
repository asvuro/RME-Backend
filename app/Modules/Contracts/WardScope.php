<?php

namespace App\Modules\Contracts;

use Modules\Auth\Models\User;

/**
 * Kontrak least-privilege per ward (#3 ANALISIS_RME_BACKEND.md): role
 * admin/petugas saja tidak cukup granular — petugas seharusnya hanya boleh
 * menulis data kunjungan/bed/tagihan/dispense/stok pada ward tempat dia
 * ditugaskan (Modules\General{Staff,Doctor,Nurse}WardAssignment).
 *
 * Implementasi: App\Support\WardAccessResolver.
 */
interface WardScope
{
    /**
     * ID ward tempat user ditugaskan (gabungan dari StaffMember/Doctor/Nurse
     * ward assignment). Kosong bila user tidak terhubung ke Employee, atau
     * Employee-nya tidak punya assignment ward apa pun.
     *
     * @return list<int>
     */
    public function assignedWardIds(int $userId): array;

    /**
     * Boleh menulis ke entitas berward $wardId? Admin selalu boleh. $wardId
     * null (entitas tidak terikat ward, mis. kunjungan rawat jalan) selalu
     * boleh — scope ward cuma berlaku untuk entitas yang memang punya ward.
     */
    public function canAccessWard(User $user, ?int $wardId): bool;

    /**
     * Terapkan cakupan baca ward yang sama dengan canAccessWard() pada query:
     * admin bebas; user belum ter-assign ward apa pun dianggap belum masuk
     * cakupan scoping (lihat semua); user berassignment hanya melihat ward
     * miliknya. Satu implementasi supaya scope baca & tulis tidak bisa
     * saling menyimpang lagi.
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<TModel>  $query
     * @param  string  $column  nama kolom ward pada tabel query (mis. 'ward_id')
     * @return \Illuminate\Database\Eloquent\Builder<TModel>
     */
    public function applyReadScope($query, string $column, User $user);
}
