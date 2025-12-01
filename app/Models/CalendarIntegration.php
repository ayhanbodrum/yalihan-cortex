<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class CalendarIntegration extends Model
{
    use HasFactory;

    protected $table = 'calendar_integrations';

    protected $fillable = [
        'user_id',
        'provider',
        'external_calendar_id',
        'access_token_encrypted',
        'refresh_token_encrypted',
        'expires_at',
        'meta',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'meta' => 'array',
    ];

    public function setAccessTokenAttribute($value)
    {
        $this->attributes['access_token_encrypted'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getAccessTokenAttribute(): ?string
    {
        $enc = $this->attributes['access_token_encrypted'] ?? null;
        if (! $enc) {
            return null;
        }
        try {
            return Crypt::decryptString($enc);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function setRefreshTokenAttribute($value)
    {
        $this->attributes['refresh_token_encrypted'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getRefreshTokenAttribute(): ?string
    {
        $enc = $this->attributes['refresh_token_encrypted'] ?? null;
        if (! $enc) {
            return null;
        }
        try {
            return Crypt::decryptString($enc);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
