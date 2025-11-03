<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\OzellikKategori;
use App\Models\User;

class OzellikKategoriPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, OzellikKategori $kategori): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function update(User $user, OzellikKategori $kategori): bool
    {
        return $this->canManage($user);
    }

    public function delete(User $user, OzellikKategori $kategori): bool
    {
        return $this->canManage($user);
    }

    public function restore(User $user, OzellikKategori $kategori): bool
    {
        return $this->canManage($user);
    }

    public function forceDelete(User $user, OzellikKategori $kategori): bool
    {
        return $this->canManage($user);
    }

    private function canManage(User $user): bool
    {
        if (! $user->role) {
            return false;
        }

        return in_array($user->role->name, [
            UserRole::SUPERADMIN->value,
            UserRole::EDITOR->value,
        ], true);
    }
}
