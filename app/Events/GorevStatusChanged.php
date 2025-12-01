<?php

namespace App\Events;

use App\Modules\TakimYonetimi\Models\Gorev;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Görev Durumu Değişti Event
 *
 * Context7: Takım Yönetimi Otomasyonu - Temel Event Sistemi
 * Görev durumu (status) değiştiğinde bu event fırlatılır.
 */
class GorevStatusChanged
{
    use Dispatchable, SerializesModels;

    /**
     * Durumu değişen görev
     */
    public Gorev $gorev;

    /**
     * Eski durum
     */
    public string $oldStatus;

    /**
     * Yeni durum
     */
    public string $newStatus;

    /**
     * Create a new event instance.
     *
     * @param Gorev $gorev
     * @param string $oldStatus
     * @param string $newStatus
     */
    public function __construct(Gorev $gorev, string $oldStatus, string $newStatus)
    {
        $this->gorev = $gorev;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
