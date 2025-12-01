<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IlanPrivateAudit extends Model
{
    protected $table = 'ilan_private_audits';

    protected $fillable = ['ilan_id', 'user_id', 'changes'];

    protected $casts = ['changes' => 'array'];
}
