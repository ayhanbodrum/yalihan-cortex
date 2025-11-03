<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\IlanKategoriYayinTipi;
use App\Models\User;

class IlanKategoriYayinTipiPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, IlanKategoriYayinTipi $yayinTipi): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(User $user, IlanKategoriYayinTipi $yayinTipi): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, IlanKategoriYayinTipi $yayinTipi): bool
    {
        return $this->canManage($user);
    }

    public function restore(User $user, IlanKategoriYayinTipi $yayinTipi): bool
    {
        return $this->canManage($user);
    }

    public function forceDelete(User $user, IlanKategoriYayinTipi $yayinTipi): bool
    {
        return $this->canManage($user);
    }

    private function canManage(User $user): bool
    {
        if (app()->environment('local')) {
            return true;
        }
        // Öncelikle açıkça danışman (sadece danışman hakları) ise reddet
        if (method_exists($user, 'hasRole') && $user->hasRole('danisman')) {
            // Kullanıcı sadece danışman mı kontrolü: admin/editor rollerine sahip değilse direkt red
            $elevated = ['admin', 'editor', 'superadmin'];
            foreach ($elevated as $e) {
                if (method_exists($user, 'hasRole') && $user->hasRole($e)) {
                    return true;
                }
            }

            return false;
        }

        // Spatie roles varsa
        // Yönetim yetkisi olan roller (danisman hariç)
        $roleNames = [
            'admin', 'superadmin', 'editor',
            UserRole::SUPERADMIN->value,
            UserRole::EDITOR->value,
        ];

        if (method_exists($user, 'hasAnyRole')) {
            if ($user->hasAnyRole($roleNames)) {
                return true;
            }
        } elseif (method_exists($user, 'hasRole')) {
            foreach ($roleNames as $r) {
                if ($user->hasRole($r)) {
                    return true;
                }
            }
        } elseif (property_exists($user, 'role') && $user->role) {
            return in_array($user->role->name, $roleNames, true);
        }

        return false;
    }
}
