<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Feature;
use App\Models\User;

class FeaturePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, Feature $feature): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(User $user, Feature $feature): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, Feature $feature): bool
    {
        return $this->canManage($user);
    }

    public function restore(User $user, Feature $feature): bool
    {
        return $this->canManage($user);
    }

    public function forceDelete(User $user, Feature $feature): bool
    {
        return $this->canManage($user);
    }

    private function canManage(User $user): bool
    {
        // 1) Spatie permission (hasAnyRole) mevcutsa öncelikle onu kullan
        $accepted = [
            'admin', 'superadmin', 'editor', // string bazlı (spatie veya legacy)
            UserRole::SUPERADMIN->value,
            UserRole::EDITOR->value,
        ];

        if (method_exists($user, 'hasAnyRole')) {
            if ($user->hasAnyRole($accepted)) {
                return true;
            }
        }

        // 2) Tekil hasRole metodu varsa
        if (method_exists($user, 'hasRole')) {
            foreach ($accepted as $roleName) {
                if ($user->hasRole($roleName)) {
                    return true;
                }
            }
        }

        // 3) Geleneksel $user->role ilişkisi (role tablosu) üzerinden
        if ($user->role && in_array($user->role->name, $accepted, true)) {
            return true;
        }

        return false;
    }
}
