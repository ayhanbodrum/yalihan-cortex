<?php

namespace App\Services;

use App\Models\Ilan;

class IlanService
{
    public function create(array $data): Ilan
    {
        return Ilan::create($data);
    }

    public function update(Ilan $ilan, array $data): Ilan
    {
        $ilan->update($data);

        return $ilan;
    }

    public function delete(Ilan $ilan): bool
    {
        return (bool) $ilan->delete();
    }

    public function find(int $id): ?Ilan
    {
        return Ilan::find($id);
    }
}
