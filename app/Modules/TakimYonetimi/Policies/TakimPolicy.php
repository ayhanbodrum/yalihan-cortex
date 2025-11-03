<?php

namespace App\Modules\TakimYonetimi\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TakimPolicy
{
    use HandlesAuthorization;

    /**
     * Takım yönetimi yetkisi
     */
    public function manageTeam(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }

    /**
     * Takım performansını görüntüleme yetkisi
     */
    public function viewPerformance(User $user, ?int $takimId = null): bool
    {
        // Super admin ve admin tüm takım performanslarını görebilir
        if (in_array($user->role, ['super_admin', 'admin'])) {
            return true;
        }

        // Danışman sadece kendi performansını görebilir
        if ($user->role === 'danisman') {
            return $takimId === null || $takimId === $user->id;
        }

        return false;
    }

    /**
     * Takım üyesi ekleme yetkisi
     */
    public function addMember(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }

    /**
     * Takım üyesi çıkarma yetkisi
     */
    public function removeMember(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }

    /**
     * Takım üyesi düzenleme yetkisi
     */
    public function editMember(User $user, ?int $memberId = null): bool
    {
        // Super admin ve admin tüm üyeleri düzenleyebilir
        if (in_array($user->role, ['super_admin', 'admin'])) {
            return true;
        }

        // Danışman sadece kendi bilgilerini düzenleyebilir
        if ($user->role === 'danisman') {
            return $memberId === null || $memberId === $user->id;
        }

        return false;
    }

    /**
     * Takım raporları görüntüleme yetkisi
     */
    public function viewReports(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }

    /**
     * Takım istatistikleri görüntüleme yetkisi
     */
    public function viewStatistics(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }

    /**
     * Takım ayarları düzenleme yetkisi
     */
    public function editSettings(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }

    /**
     * Takım üyesi performans değerlendirmesi yetkisi
     */
    public function evaluatePerformance(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }

    /**
     * Takım üyesi izin yönetimi yetkisi
     */
    public function manageLeave(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }

    /**
     * Takım üyesi çalışma saati yönetimi yetkisi
     */
    public function manageWorkHours(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }
}
