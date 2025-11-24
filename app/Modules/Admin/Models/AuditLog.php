<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'method',
        'route_name',
        'url',
        'ip',
        'status_code',
        'module',
        'controller',
        'action',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}