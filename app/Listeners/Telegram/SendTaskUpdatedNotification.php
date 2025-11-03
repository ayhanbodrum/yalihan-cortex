<?php

namespace App\Listeners\Telegram;

use App\Models\Gorev;
use App\Modules\TakimYonetimi\Services\TelegramBotService;

class SendTaskUpdatedNotification
{
    public function handle(Gorev $gorev): void
    {
        try {
            $service = app(TelegramBotService::class);
            $assignee = $gorev->user;
            if ($assignee && ! empty($assignee->telegram_chat_id)) {
                $service->sendMessage($assignee->telegram_chat_id, 'Görev Güncellendi: '.$gorev->gorev_adi.' – Durum: '.($gorev->status ?? '-'));
            }

            // Takım kanal bildirimi (opsiyonel)
            if (method_exists($gorev, 'proje') && $gorev->proje && method_exists($gorev->proje, 'team')) {
                $team = $gorev->proje->team;
                if ($team && ! empty($team->telegram_channel_id)) {
                    $service->sendMessage($team->telegram_channel_id, 'Takım Görevi Güncellendi: '.$gorev->gorev_adi);
                }
            }
        } catch (\Throwable $e) {
        }
    }
}
