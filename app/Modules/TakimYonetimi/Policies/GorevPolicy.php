<?php

namespace App\Modules\TakimYonetimi\Policies;

use App\Models\User;
use App\Modules\TakimYonetimi\Models\Gorev;
use Illuminate\Auth\Access\HandlesAuthorization;

class GorevPolicy
{
    use HandlesAuthorization;

    /**
     * Tüm görevleri görüntüleme yetkisi
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin', 'danisman']);
    }

    /**
     * Belirli bir görevi görüntüleme yetkisi
     */
    public function view(User $user, Gorev $gorev): bool
    {
        // Super admin ve admin tüm görevleri görebilir
        if (in_array($user->role, ['super_admin', 'admin'])) {
            return true;
        }

        // Danışman sadece kendi görevlerini görebilir
        if ($user->role === 'danisman') {
            return $gorev->danisman_id === $user->id;
        }

        return false;
    }

    /**
     * Yeni görev oluşturma yetkisi
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }

    /**
     * Görev düzenleme yetkisi
     */
    public function update(User $user, Gorev $gorev): bool
    {
        // Super admin ve admin tüm görevleri düzenleyebilir
        if (in_array($user->role, ['super_admin', 'admin'])) {
            return true;
        }

        // Danışman sadece kendi görevlerini düzenleyebilir
        if ($user->role === 'danisman') {
            return $gorev->danisman_id === $user->id &&
                   in_array($gorev->status, ['bekliyor', 'devam_ediyor']);
        }

        return false;
    }

    /**
     * Görev silme yetkisi
     */
    public function delete(User $user, Gorev $gorev): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }

    /**
     * Görev atama yetkisi
     */
    public function assign(User $user, Gorev $gorev): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }

    /**
     * Görev statusu güncelleme yetkisi
     */
    public function updateStatus(User $user, Gorev $gorev): bool
    {
        // Super admin ve admin tüm görevlerin statusunu güncelleyebilir
        if (in_array($user->role, ['super_admin', 'admin'])) {
            return true;
        }

        // Danışman sadece kendi görevlerinin statusunu güncelleyebilir
        if ($user->role === 'danisman') {
            return $gorev->danisman_id === $user->id;
        }

        return false;
    }

    /**
     * Görev tamamlama yetkisi
     */
    public function complete(User $user, Gorev $gorev): bool
    {
        // Super admin ve admin tüm görevleri tamamlayabilir
        if (in_array($user->role, ['super_admin', 'admin'])) {
            return true;
        }

        // Danışman sadece kendi görevlerini tamamlayabilir
        if ($user->role === 'danisman') {
            return $gorev->danisman_id === $user->id &&
                   in_array($gorev->status, ['bekliyor', 'devam_ediyor']);
        }

        return false;
    }

    /**
     * Görev dosyası ekleme yetkisi
     */
    public function addFile(User $user, Gorev $gorev): bool
    {
        // Super admin ve admin tüm görevlere dosya ekleyebilir
        if (in_array($user->role, ['super_admin', 'admin'])) {
            return true;
        }

        // Danışman sadece kendi görevlerine dosya ekleyebilir
        if ($user->role === 'danisman') {
            return $gorev->danisman_id === $user->id;
        }

        return false;
    }

    /**
     * Görev raporları görüntüleme yetkisi
     */
    public function viewReports(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }

    /**
     * Toplu görev işlemleri yetkisi
     */
    public function bulkOperations(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'admin']);
    }
}
