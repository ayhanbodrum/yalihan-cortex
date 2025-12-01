<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bildirim extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'baslik',
        'mesaj',
        'tip',
        'kanal',
        'kategori',
        'data',
        'okundu',
        'okunma_tarihi',
        'gonderim_tarihi',
        'status',
    ];

    protected $casts = [
        'data' => 'array',
        'okundu' => 'boolean',
        'status' => 'boolean',
        'okunma_tarihi' => 'datetime',
        'gonderim_tarihi' => 'datetime',
    ];

    /**
     * Kullanıcı ilişkisi
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Bildirimi okundu olarak işaretle
     */
    public function markAsRead()
    {
        $this->okundu = true;
        $this->okunma_tarihi = now();
        $this->save();
    }

    /**
     * Bildirimi gönder
     */
    public function send()
    {
        $this->gonderim_tarihi = now();
        $this->save();

        // Kanal bazında gönderim işlemi
        switch ($this->kanal) {
            case 'email':
                $this->sendEmail();
                break;
            case 'sms':
                $this->sendSms();
                break;
            case 'push':
                $this->sendPush();
                break;
        }
    }

    /**
     * Email gönderimi
     */
    protected function sendEmail()
    {
        // Email gönderim servisi çağrılacak
        // Mail::to($this->user->email)->send(new BildirimMail($this));
    }

    /**
     * SMS gönderimi
     */
    protected function sendSms()
    {
        // SMS gönderim servisi çağrılacak
        // SmsService::send($this->user->phone, $this->mesaj);
    }

    /**
     * Push notification gönderimi
     */
    protected function sendPush()
    {
        // Push notification servisi çağrılacak
        // PushService::send($this->user, $this->baslik, $this->mesaj);
    }

    /**
     * Scope: Okunmamış bildirimler
     */
    public function scopeOkunmamis($query)
    {
        return $query->where('okundu', false);
    }

    /**
     * Scope: Belirli kullanıcının bildirimleri
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Belirli tipte bildirimler
     */
    public function scopeByTip($query, $tip)
    {
        return $query->where('tip', $tip);
    }

    /**
     * Scope: Belirli kategoride bildirimler
     */
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Scope: Aktif bildirimler
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope: Son 7 gün
     */
    public function scopeSon7Gun($query)
    {
        return $query->where('created_at', '>=', now()->subDays(7));
    }

    /**
     * Scope: Son 30 gün
     */
    public function scopeSon30Gun($query)
    {
        return $query->where('created_at', '>=', now()->subDays(30));
    }
}
