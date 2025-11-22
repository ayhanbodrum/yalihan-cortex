<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Ilan;
use App\Models\User;

class IlanPolicy
{
    public function viewPrivateListingData(User $user, Ilan $ilan): bool
    {
        if ($user->role && $user->role->name === UserRole::SUPERADMIN->value) {
            return true;
        }
        return $user->id === ($ilan->danisman_id ?? 0);
    }
}