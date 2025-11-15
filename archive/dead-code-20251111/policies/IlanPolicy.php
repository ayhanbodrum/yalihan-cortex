<?php

namespace App\Policies;

use App\Models\User;
use App\Modules\Emlak\Models\Ilan;

class IlanPolicy
{
    /**
     * Policy sınıfı çağrılmadan önce çalışan metod.
     * Süper admin her zaman tüm kaynaklara erişebilir.
     */
    public function before(User $user, $ability)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     * Geçici olarak tüm kullanıcılara erişim izni veriyoruz
     */
    public function viewAny(User $user): bool
    {
        // Geçici olarak tüm kullanıcılara erişim izni
        return true;
        // Normalde sadece danışmanlar erişebilir
        // return $user->isDanisman();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ilan $ilan): bool
    {
        // Admin tüm ilanları görüntüleyebilir
        if ($user->isAdmin()) {
            return true;
        }

        // Danışman kendi ilanlarını görüntüleyebilir
        return $user->isDanisman() && $ilan->danisman_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Geçici olarak tüm kullanıcılara erişim izni veriyoruz
        return true;
        // Normalde sadece danışmanlar erişebilir
        // return $user->isDanisman();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ilan $ilan): bool
    {
        // Admin tüm ilanları güncelleyebilir
        if ($user->isAdmin()) {
            return true;
        }

        // Danışman sadece kendi ilanlarını güncelleyebilir
        return $user->isDanisman() && $ilan->danisman_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ilan $ilan): bool
    {
        // Danışman sadece kendi ilanlarını silebilir
        return $user->isDanisman() && $ilan->danisman_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ilan $ilan): bool
    {
        // Danışman sadece kendi ilanlarını geri yükleyebilir
        return $user->isDanisman() && $ilan->danisman_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ilan $ilan): bool
    {
        // Kalıcı silme işlemi sadece süper admin tarafından yapılabilir
        // before metodu zaten süper admin kontrolünü yapıyor
        return false; // Danışmanlar kalıcı silme işlemi yapamaz
    }
}
