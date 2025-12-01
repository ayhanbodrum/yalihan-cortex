<?php

namespace App\Modules\TakimYonetimi\Services;

use App\Models\User;
use App\Modules\TakimYonetimi\Models\Gorev;
use App\Modules\TakimYonetimi\Models\GorevTakip;
use App\Modules\TakimYonetimi\Models\TakimUyesi;
use App\Services\AudioTranscriptionService;
use App\Services\VoiceCommandProcessor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    protected string $botToken;

    protected string $botUsername;

    protected string $apiBaseUrl;

    protected array $commands = [];

    protected array $adminUsers = [];

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token', '');
        $this->botUsername = config('services.telegram.bot_username', '');
        $this->apiBaseUrl = "https://api.telegram.org/bot{$this->botToken}";

        $this->initializeCommands();
        $this->loadAdminUsers();
    }

    /**
     * Bot komutlarını başlat
     */
    protected function initializeCommands(): void
    {
        $this->commands = [
            '/start' => 'Bot\'u başlat ve yardım menüsünü göster',
            '/help' => 'Mevcut komutları listele',
            '/chatid' => 'Chat ID\'nizi öğrenin',
            '/gorevler' => 'Aktif görevlerinizi listele',
            '/gorev_baslat' => 'Bir görevi başlatın',
            '/gorev_tamamla' => 'Bir görevi tamamlayın',
            '/gorev_durdur' => 'Bir görevi durdurun',
            '/status' => 'Mevcut görev statusunuzu göster',
            '/performans' => 'Performans istatistiklerinizi göster',
            '/admin_gorev_ata' => '[ADMIN] Görev atama',
            '/admin_gorev_listesi' => '[ADMIN] Tüm görevleri listele',
            '/admin_takim_status' => '[ADMIN] Takım statusu',
            '/admin_rapor' => '[ADMIN] Performans raporu',
        ];
    }

    /**
     * Admin kullanıcıları yükle
     */
    protected function loadAdminUsers(): void
    {
        // Role ID'leri kontrol et (1: super_admin, 2: admin)
        $this->adminUsers = User::whereIn('role_id', [1, 2])
            ->pluck('telegram_chat_id', 'id')
            ->filter()
            ->toArray();
    }

    /**
     * Webhook mesajını işle
     */
    public function handleWebhook(array $data): void
    {
        try {
            if (! isset($data['message'])) {
                return;
            }

            $message = $data['message'];
            $chatId = $message['chat']['id'];
            $text = $message['text'] ?? '';
            $from = $message['from'] ?? [];

            Log::info('Telegram webhook received', [
                'chat_id' => $chatId,
                'text' => $text,
                'from' => $from,
            ]);

            // Erişim kontrolü - Sadece Yalıhan Emlak ekibi
            $accessCheck = $this->checkAccess($chatId);
            if (! $accessCheck['granted']) {
                $this->sendMessage($chatId, "🚫 *Erişim Reddedildi*\n\n" . $accessCheck['reason']);
                Log::warning('Unauthorized Telegram access attempt', [
                    'chat_id' => $chatId,
                    'reason' => $accessCheck['reason'],
                    'user' => $accessCheck['user']?->name ?? 'Unknown',
                ]);

                return;
            }

            // Voice mesaj kontrolü
            if (isset($message['voice'])) {
                $this->processVoiceMessage($chatId, $message['voice'], $from);
                return;
            }

            // Erişim onaylandı, mesajı işle
            if (str_starts_with($text, '/')) {
                $this->processCommand($chatId, $text, $from);
            } else {
                $this->processMessage($chatId, $text, $from);
            }
        } catch (\Exception $e) {
            Log::error('Telegram webhook error: ' . $e->getMessage());
        }
    }

    /**
     * Voice mesaj işle (Context7: C7-VOICE-TO-CRM-2025-12-01)
     */
    protected function processVoiceMessage(int $chatId, array $voice, array $from): void
    {
        try {
            $this->sendMessage($chatId, "🎤 Sesli not alınıyor...");

            // Kullanıcıyı bul
            $user = $this->findUserByTelegram($from);
            if (!$user) {
                $this->sendMessage($chatId, "❌ Kullanıcı bulunamadı. Lütfen /start komutu ile başlayın.");
                return;
            }

            $audioService = new AudioTranscriptionService();
            $commandProcessor = new VoiceCommandProcessor();

            // 1. Voice dosyasını indir
            $fileId = $voice['file_id'] ?? null;
            if (!$fileId) {
                $this->sendMessage($chatId, "❌ Ses dosyası bulunamadı.");
                return;
            }

            $localFilePath = null;
            try {
                $localFilePath = $audioService->downloadTelegramVoice($fileId, $this->botToken);
            } catch (\Exception $e) {
                Log::error('Voice download error', ['error' => $e->getMessage()]);
                $this->sendMessage($chatId, "❌ Ses dosyası indirilemedi: " . $e->getMessage());
                return;
            }

            // 2. Transkript et
            $transcript = null;
            try {
                $transcript = $audioService->transcribe($localFilePath);
            } catch (\Exception $e) {
                Log::error('Voice transcription error', ['error' => $e->getMessage()]);
                $this->sendMessage($chatId, "❌ Ses yazıya çevrilemedi: " . $e->getMessage());
                $audioService->cleanup($localFilePath);
                return;
            } finally {
                // Geçici dosyayı temizle
                if ($localFilePath) {
                    $audioService->cleanup($localFilePath);
                }
            }

            if (empty($transcript)) {
                $this->sendMessage($chatId, "❌ Ses dosyasından metin çıkarılamadı.");
                return;
            }

            // 3. Komutu analiz et
            $commandData = $commandProcessor->process($transcript, $user->id);

            // 4. CRM aksiyonunu uygula
            $result = $commandProcessor->executeAction($commandData, $user->id);

            if ($result['success']) {
                $actionType = $result['action_type'] ?? 'gorusme_notu';
                $actionName = $actionType === 'gorev' ? 'Görev' : 'Görüşme Notu';
                $this->sendMessage($chatId, "✅ {$actionName} oluşturuldu!\n\n📝 Transkript: " . substr($transcript, 0, 200));
            } else {
                $this->sendMessage($chatId, "❌ İşlem başarısız: " . ($result['message'] ?? 'Bilinmeyen hata'));
            }
        } catch (\Exception $e) {
            Log::error('Voice message processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->sendMessage($chatId, "❌ Sesli not işlenirken hata oluştu: " . $e->getMessage());
        }
    }

    /**
     * Komut işle
     */
    protected function processCommand(int $chatId, string $text, array $from): void
    {
        $command = strtolower(trim($text));

        switch ($command) {
            case '/start':
                $this->sendStartMessage($chatId);
                break;

            case '/help':
                $this->sendHelpMessage($chatId);
                break;

            case '/chatid':
                $this->sendChatIdMessage($chatId);
                break;

            case '/gorevler':
                $this->sendUserTasks($chatId, $from);
                break;

            case '/status':
                $this->sendUserStatus($chatId, $from);
                break;

            case '/performans':
                $this->sendUserPerformance($chatId, $from);
                break;

            case '/admin_gorev_ata':
                $this->handleAdminTaskAssignment($chatId, $from);
                break;

            case '/admin_gorev_listesi':
                $this->sendAdminTaskList($chatId, $from);
                break;

            case '/admin_takim_status':
                $this->sendTeamStatus($chatId, $from);
                break;

            case '/admin_rapor':
                $this->sendPerformanceReport($chatId, $from);
                break;

            default:
                $this->sendMessage($chatId, "❌ Bilinmeyen komut: {$text}\n\n/help yazarak mevcut komutları görebilirsiniz.");
                break;
        }
    }

    /**
     * Normal mesaj işle
     */
    protected function processMessage(int $chatId, string $text, array $from): void
    {
        // Görev statusu güncelleme işlemleri
        if (preg_match('/^gorev_(\d+)_(\w+)$/', $text, $matches)) {
            $gorevId = $matches[1];
            $action = $matches[2];
            $this->handleTaskAction($chatId, $gorevId, $action, $from);

            return;
        }

        // Görev notu ekleme
        if (preg_match('/^not_(\d+):(.+)$/', $text, $matches)) {
            $gorevId = $matches[1];
            $note = trim($matches[2]);
            $this->addTaskNote($chatId, $gorevId, $note, $from);

            return;
        }

        // Yardım mesajı gönder
        $this->sendMessage($chatId, "💡 Görev işlemleri için komutları kullanın:\n\n/help yazarak mevcut komutları görebilirsiniz.");
    }

    /**
     * Başlangıç mesajı gönder
     */
    protected function sendStartMessage(int $chatId): void
    {
        // Kullanıcının chat ID'sini kaydet
        $this->registerUserChatId($chatId);

        $message = "🚀 *Yalıhan Emlak Görev Botu*'na hoş geldiniz!\n\n";
        $message .= "✅ *Chat ID'niz kaydedildi:* `{$chatId}`\n\n";
        $message .= "Bu bot ile:\n";
        $message .= "• 📋 Görevlerinizi takip edebilirsiniz\n";
        $message .= "• ⏱️ Görev sürelerini yönetebilirsiniz\n";
        $message .= "• 📊 Performansınızı izleyebilirsiniz\n";
        $message .= "• 🔔 Bildirimler alabilirsiniz\n\n";
        $message .= "💡 *Chat ID'nizi öğrenmek için:*\n";
        $message .= "• Bu mesajda gösterildiği gibi: `{$chatId}`\n";
        $message .= "• Veya @userinfobot botuna yazın\n\n";
        $message .= 'Başlamak için /help yazın.';

        $this->sendMessage($chatId, $message);
    }

    /**
     * Chat ID mesajı gönder
     */
    protected function sendChatIdMessage(int $chatId): void
    {
        $message = "🆔 *Chat ID Bilgileriniz:*\n\n";
        $message .= "📋 Chat ID: `{$chatId}`\n\n";
        $message .= "💡 *Bu ID'yi sistem yöneticinize vererek bot erişiminizi statusleştirin.*\n\n";
        $message .= "� *Alternatif yöntemler:*\n";
        $message .= "• @userinfobot botuna yazarak Chat ID'nizi öğrenebilirsiniz\n";
        $message .= "• Veya yukarıdaki Chat ID'yi kopyalayın\n\n";
        $message .= "✅ Chat ID'niz otomatik olarak kaydedildi!";

        $this->sendMessage($chatId, $message);
    }

    /**
     * Kullanıcının görevlerini gönder
     */
    protected function sendUserTasks(int $chatId, array $from): void
    {
        $user = $this->findUserByTelegram($from);
        if (! $user) {
            $this->sendMessage($chatId, '❌ Kullanıcı bulunamadı. Lütfen önce sisteme kayıt olun.');

            return;
        }

        $gorevler = Gorev::where('danisman_id', $user->id)
            ->whereIn('status', ['bekliyor', 'devam_ediyor'])
            ->orderBy('oncelik', 'desc')
            ->orderBy('deadline')
            ->get();

        if ($gorevler->isEmpty()) {
            $this->sendMessage($chatId, '✅ Aktif görev bulunmuyor.');

            return;
        }

        $message = "📋 *Aktif Görevleriniz:*\n\n";

        foreach ($gorevler as $gorev) {
            $priority = $this->getPriorityEmoji($gorev->oncelik);
            $status = $this->getStatusEmoji($gorev->status);
            $deadline = $gorev->deadline ? $gorev->deadline->format('d.m.Y H:i') : 'Belirtilmemiş';

            $message .= "{$priority} *{$gorev->baslik}*\n";
            $message .= "{$status} Durum: " . ucfirst(str_replace('_', ' ', $gorev->status)) . "\n";
            $message .= "⏰ Deadline: {$deadline}\n";
            $message .= "🆔 ID: `{$gorev->id}`\n\n";
        }

        $message .= "💡 *Hızlı İşlemler:*\n";
        $message .= "• `gorev_[ID]_baslat` - Görevi başlat\n";
        $message .= "• `gorev_[ID]_tamamla` - Görevi tamamla\n";
        $message .= "• `not_[ID]:Not` - Not ekle\n";

        $this->sendMessage($chatId, $message);
    }

    /**
     * Kullanıcı statusunu gönder
     */
    protected function sendUserStatus(int $chatId, array $from): void
    {
        $user = $this->findUserByTelegram($from);
        if (! $user) {
            $this->sendMessage($chatId, '❌ Kullanıcı bulunamadı.');

            return;
        }

        $takimUyesi = TakimUyesi::where('user_id', $user->id)->first();
        if (! $takimUyesi) {
            $this->sendMessage($chatId, '❌ Takım üyesi bulunamadı.');

            return;
        }

        $statusGorevler = Gorev::where('danisman_id', $user->id)
            ->whereIn('status', ['bekliyor', 'devam_ediyor'])
            ->count();

        $tamamlananGorevler = Gorev::where('danisman_id', $user->id)
            ->where('status', 'tamamlandi')
            ->count();

        $message = "👤 *Kullanıcı Durumu:*\n\n";
        $message .= "👤 Ad: {$user->name}\n";
        $message .= '🎯 Rol: ' . ucfirst(str_replace('_', ' ', $takimUyesi->rol)) . "\n";
        $message .= "📊 Performans: {$takimUyesi->performans_skoru}/100\n";
        $message .= "📋 Aktif Görev: {$statusGorevler}\n";
        $message .= "✅ Tamamlanan: {$tamamlananGorevler}\n";
        $message .= "🏆 Başarı Oranı: {$takimUyesi->basari_orani}%\n";

        $this->sendMessage($chatId, $message);
    }

    /**
     * Kullanıcı performansını gönder
     */
    protected function sendUserPerformance(int $chatId, array $from): void
    {
        $user = $this->findUserByTelegram($from);
        if (! $user) {
            $this->sendMessage($chatId, '❌ Kullanıcı bulunamadı.');

            return;
        }

        $takimUyesi = TakimUyesi::where('user_id', $user->id)->first();
        if (! $takimUyesi) {
            $this->sendMessage($chatId, '❌ Takım üyesi bulunamadı.');

            return;
        }

        $buAyGorevler = Gorev::where('danisman_id', $user->id)
            ->where('status', 'tamamlandi')
            ->whereMonth('updated_at', now()->month)
            ->count();

        $message = "📊 *Performans Raporu:*\n\n";
        $message .= "👤 {$user->name}\n";
        $message .= '📅 ' . now()->format('F Y') . "\n\n";
        $message .= "📋 Bu Ay Tamamlanan: {$buAyGorevler}\n";
        $message .= "🏆 Genel Başarı: {$takimUyesi->basari_orani}%\n";
        $message .= "⭐ Performans Skoru: {$takimUyesi->performans_skoru}/100\n";
        $message .= '⏱️ Ortalama Süre: ' . ($takimUyesi->ortalama_sure_formatli ?? 'N/A') . "\n";

        $this->sendMessage($chatId, $message);
    }

    /**
     * Admin görev atama işlemi
     */
    protected function handleAdminTaskAssignment(int $chatId, array $from): void
    {
        if (! $this->isAdmin($chatId)) {
            $this->sendMessage($chatId, '❌ Bu komutu sadece admin kullanıcılar kullanabilir.');

            return;
        }

        $message = "📝 *Görev Atama:*\n\n";
        $message .= "Görev atamak için aşağıdaki formatta mesaj gönderin:\n\n";
        $message .= "`gorev_ata:[danışman_id]:[görev_başlığı]:[açıklama]:[öncelik]:[deadline]`\n\n";
        $message .= "Örnek:\n";
        $message .= "`gorev_ata:123:Müşteri Ziyareti:Müşteri ile görüşme yapılacak:yuksek:2025-09-01 14:00`\n\n";
        $message .= "Öncelikler: acil, yuksek, normal, dusuk\n";

        $this->sendMessage($chatId, $message);
    }

    /**
     * Admin görev listesini gönder
     */
    protected function sendAdminTaskList(int $chatId, array $from): void
    {
        if (! $this->isAdmin($chatId)) {
            $this->sendMessage($chatId, '❌ Bu komutu sadece admin kullanıcılar kullanabilir.');

            return;
        }

        $gorevler = Gorev::with(['danisman', 'musteri'])
            ->orderBy('oncelik', 'desc')
            ->orderBy('deadline')
            ->limit(10)
            ->get();

        if ($gorevler->isEmpty()) {
            $this->sendMessage($chatId, '📋 Henüz görev bulunmuyor.');

            return;
        }

        $message = "📋 *Son 10 Görev:*\n\n";

        foreach ($gorevler as $gorev) {
            $priority = $this->getPriorityEmoji($gorev->oncelik);
            $status = $this->getStatusEmoji($gorev->status);
            $danisman = $gorev->danisman ? $gorev->danisman->name : 'Atanmamış';
            $deadline = $gorev->deadline ? $gorev->deadline->format('d.m.Y H:i') : 'Belirtilmemiş';

            $message .= "{$priority} *{$gorev->baslik}*\n";
            $message .= "👤 Danışman: {$danisman}\n";
            $message .= "{$status} Durum: " . ucfirst(str_replace('_', ' ', $gorev->status)) . "\n";
            $message .= "⏰ Deadline: {$deadline}\n";
            $message .= "🆔 ID: `{$gorev->id}`\n\n";
        }

        $this->sendMessage($chatId, $message);
    }

    /**
     * Takım statusunu gönder
     */
    protected function sendTeamStatus(int $chatId, array $from): void
    {
        if (! $this->isAdmin($chatId)) {
            $this->sendMessage($chatId, '❌ Bu komutu sadece admin kullanıcılar kullanabilir.');

            return;
        }

        $toplamUye = TakimUyesi::where('status', 'aktif')->count();
        $statusGorev = Gorev::whereIn('status', ['bekliyor', 'devam_ediyor'])->count();
        $tamamlananGorev = Gorev::where('status', 'tamamlandi')->count();
        $gecikenGorev = Gorev::where('deadline', '<', now())->whereIn('status', ['bekliyor', 'devam_ediyor'])->count();

        $message = "👥 *Takım Durumu:*\n\n";
        $message .= "👤 Aktif Üye: {$toplamUye}\n";
        $message .= "📋 Aktif Görev: {$statusGorev}\n";
        $message .= "✅ Tamamlanan: {$tamamlananGorev}\n";
        $message .= "⚠️ Geciken: {$gecikenGorev}\n";
        $message .= '📊 Başarı Oranı: ' . ($tamamlananGorev > 0 ? round(($tamamlananGorev / ($statusGorev + $tamamlananGorev)) * 100) : 0) . "%\n";

        $this->sendMessage($chatId, $message);
    }

    /**
     * Performans raporu gönder
     */
    protected function sendPerformanceReport(int $chatId, array $from): void
    {
        if (! $this->isAdmin($chatId)) {
            $this->sendMessage($chatId, '❌ Bu komutu sadece admin kullanıcılar kullanabilir.');

            return;
        }

        $enIyiPerformans = TakimUyesi::with('user')
            ->where('status', 'active')
            ->orderBy('performans_skoru', 'desc')
            ->limit(5)
            ->get();

        $message = "🏆 *En İyi Performans:*\n\n";

        foreach ($enIyiPerformans as $index => $uye) {
            $rank = $index + 1;
            $rankEmoji = $rank === 1 ? '🥇' : ($rank === 2 ? '🥈' : ($rank === 3 ? '🥉' : '4️⃣'));

            $message .= "{$rankEmoji} *{$uye->user->name}*\n";
            $message .= "📊 Skor: {$uye->performans_skoru}/100\n";
            $message .= "✅ Başarı: {$uye->basari_orani}%\n";
            $message .= "📋 Görev: {$uye->toplam_gorev}\n\n";
        }

        $this->sendMessage($chatId, $message);
    }

    /**
     * Görev işlemi yap
     */
    protected function handleTaskAction(int $chatId, int $gorevId, string $action, array $from): void
    {
        $user = $this->findUserByTelegram($from);
        if (! $user) {
            $this->sendMessage($chatId, '❌ Kullanıcı bulunamadı.');

            return;
        }

        $gorev = Gorev::find($gorevId);
        if (! $gorev) {
            $this->sendMessage($chatId, '❌ Görev bulunamadı.');

            return;
        }

        if ($gorev->danisman_id !== $user->id) {
            $this->sendMessage($chatId, '❌ Bu görevi sadece atanan danışman yönetebilir.');

            return;
        }

        switch ($action) {
            case 'baslat':
                $this->startTask($chatId, $gorev, $user);
                break;

            case 'tamamla':
                $this->completeTask($chatId, $gorev, $user);
                break;

            case 'durdur':
                $this->pauseTask($chatId, $gorev, $user);
                break;

            default:
                $this->sendMessage($chatId, "❌ Bilinmeyen işlem: {$action}");
                break;
        }
    }

    /**
     * Görevi başlat
     */
    protected function startTask(int $chatId, Gorev $gorev, User $user): void
    {
        if ($gorev->status === 'devam_ediyor') {
            $this->sendMessage($chatId, '⚠️ Görev zaten devam ediyor.');

            return;
        }

        try {
            $gorev->update(['status' => 'devam_ediyor']);

            // Görev takibi oluştur
            GorevTakip::create([
                'gorev_id' => $gorev->id,
                'user_id' => $user->id,
                'status' => 'basladi',
                'baslangic_zamani' => now(),
                'notlar' => 'Telegram bot üzerinden başlatıldı',
            ]);

            $this->sendMessage($chatId, "✅ Görev başarıyla başlatıldı!\n\n📋 *{$gorev->baslik}*\n⏰ Başlangıç: " . now()->format('d.m.Y H:i'));

            // Admin'lere bildirim gönder
            $this->notifyAdmins("🚀 Görev Başlatıldı\n\n📋 {$gorev->baslik}\n👤 {$user->name}\n⏰ " . now()->format('d.m.Y H:i'));
        } catch (\Exception $e) {
            Log::error('Görev başlatma hatası: ' . $e->getMessage());
            $this->sendMessage($chatId, '❌ Görev başlatılırken hata oluştu.');
        }
    }

    /**
     * Görevi tamamla
     */
    protected function completeTask(int $chatId, Gorev $gorev, User $user): void
    {
        if ($gorev->status === 'tamamlandi') {
            $this->sendMessage($chatId, '⚠️ Görev zaten tamamlanmış.');

            return;
        }

        try {
            $gorev->update(['status' => 'tamamlandi']);

            // Görev takibini güncelle
            $statusTakip = GorevTakip::where('gorev_id', $gorev->id)
                ->where('user_id', $user->id)
                ->where('status', 'devam_ediyor')
                ->first();

            if ($statusTakip) {
                $statusTakip->update([
                    'status' => 'tamamlandi',
                    'bitis_zamani' => now(),
                    'notlar' => ($statusTakip->notlar ? $statusTakip->notlar . "\n" : '') . 'Telegram bot üzerinden tamamlandı',
                ]);
            }

            $this->sendMessage($chatId, "🎉 Görev başarıyla tamamlandı!\n\n📋 *{$gorev->baslik}*\n⏰ Tamamlanma: " . now()->format('d.m.Y H:i'));

            // Admin'lere bildirim gönder
            $this->notifyAdmins("🎉 Görev Tamamlandı\n\n📋 {$gorev->baslik}\n👤 {$user->name}\n⏰ " . now()->format('d.m.Y H:i'));
        } catch (\Exception $e) {
            Log::error('Görev tamamlama hatası: ' . $e->getMessage());
            $this->sendMessage($chatId, '❌ Görev tamamlanırken hata oluştu.');
        }
    }

    /**
     * Görevi durdur
     */
    protected function pauseTask(int $chatId, Gorev $gorev, User $user): void
    {
        if ($gorev->status !== 'devam_ediyor') {
            $this->sendMessage($chatId, '⚠️ Sadece devam eden görevler durdurulabilir.');

            return;
        }

        try {
            $gorev->update(['status' => 'beklemede']);

            // Görev takibini güncelle
            $statusTakip = GorevTakip::where('gorev_id', $gorev->id)
                ->where('user_id', $user->id)
                ->where('status', 'devam_ediyor')
                ->first();

            if ($statusTakip) {
                $statusTakip->update([
                    'status' => 'durduruldu',
                    'bitis_zamani' => now(),
                    'notlar' => ($statusTakip->notlar ? $statusTakip->notlar . "\n" : '') . 'Telegram bot üzerinden durduruldu',
                ]);
            }

            $this->sendMessage($chatId, "⏸️ Görev durduruldu!\n\n📋 *{$gorev->baslik}*\n⏰ Durdurulma: " . now()->format('d.m.Y H:i'));

            // Admin'lere bildirim gönder
            $this->notifyAdmins("⏸️ Görev Durduruldu\n\n📋 {$gorev->baslik}\n👤 {$user->name}\n⏰ " . now()->format('d.m.Y H:i'));
        } catch (\Exception $e) {
            Log::error('Görev durdurma hatası: ' . $e->getMessage());
            $this->sendMessage($chatId, '❌ Görev durdurulurken hata oluştu.');
        }
    }

    /**
     * Göreve not ekle
     */
    protected function addTaskNote(int $chatId, int $gorevId, string $note, array $from): void
    {
        $user = $this->findUserByTelegram($from);
        if (! $user) {
            $this->sendMessage($chatId, '❌ Kullanıcı bulunamadı.');

            return;
        }

        $gorev = Gorev::find($gorevId);
        if (! $gorev) {
            $this->sendMessage($chatId, '❌ Görev bulunamadı.');

            return;
        }

        if ($gorev->danisman_id !== $user->id) {
            $this->sendMessage($chatId, '❌ Bu göreve sadece atanan danışman not ekleyebilir.');

            return;
        }

        try {
            // Görev takibine not ekle
            $statusTakip = GorevTakip::where('gorev_id', $gorev->id)
                ->where('user_id', $user->id)
                ->whereIn('status', ['basladi', 'devam_ediyor'])
                ->first();

            if ($statusTakip) {
                $statusTakip->update([
                    'notlar' => ($statusTakip->notlar ? $statusTakip->notlar . "\n" : '') . '📝 ' . now()->format('d.m.Y H:i') . ": {$note}",
                ]);
            }

            $this->sendMessage($chatId, "📝 Not başarıyla eklendi!\n\n📋 *{$gorev->baslik}*\n💬 Not: {$note}");
        } catch (\Exception $e) {
            Log::error('Not ekleme hatası: ' . $e->getMessage());
            $this->sendMessage($chatId, '❌ Not eklenirken hata oluştu.');
        }
    }

    /**
     * Kullanıcının chat ID'sini kaydet
     */
    protected function registerUserChatId(int $chatId): void
    {
        try {
            // Kullanıcıyı telegram chat ID ile bulmaya çalış
            $user = User::where('telegram_chat_id', $chatId)->first();

            if (! $user) {
                // Chat ID ile kullanıcı bulunamadı, belki username ile eşleştirebiliriz
                // Şimdilik sadece log tutalım
                Log::info("Yeni chat ID kaydedilmeye çalışıldı: {$chatId}");

                return;
            }

            // Kullanıcının telegram bilgilerini güncelle
            $user->update([
                'telegram_chat_id' => $chatId,
                'telegram_username' => $user->telegram_username, // Mevcut username'i koru
            ]);

            Log::info('Chat ID başarıyla kaydedildi', [
                'user_id' => $user->id,
                'chat_id' => $chatId,
                'user_name' => $user->name,
            ]);
        } catch (\Exception $e) {
            Log::error('Chat ID kaydetme hatası: ' . $e->getMessage(), [
                'chat_id' => $chatId,
            ]);
        }
    }

    /**
     * Erişim kontrolü - Sadece Danışmanlar ve Adminler
     */
    protected function checkAccess(int $chatId): array
    {
        $user = $this->findUserByTelegram(['id' => $chatId]);
        if (! $user) {
            return [
                'granted' => false,
                'reason' => 'Kullanıcı bulunamadı. Lütfen önce sisteme kayıt olun.',
                'user' => null,
            ];
        }

        // Önce admin kontrolü (role_id 1 veya 2)
        if (in_array($user->role_id, [1, 2])) {
            return [
                'granted' => true,
                'reason' => 'Admin erişimi onaylandı',
                'user' => $user,
                'access_type' => 'admin',
            ];
        }

        // Takım üyesi kontrolü
        $takimUyesi = TakimUyesi::where('user_id', $user->id)->first();
        if (! $takimUyesi) {
            return [
                'granted' => false,
                'reason' => 'Takım üyesi bulunamadı. Sadece danışmanlar ve adminler bu botu kullanabilir.',
                'user' => $user,
            ];
        }

        if (! $takimUyesi->statusMi()) {
            return [
                'granted' => false,
                'reason' => 'Hesabınız status değil. Lütfen yönetici ile iletişime geçin.',
                'user' => $user,
            ];
        }

        // Sadece danışman rolü kontrolü
        if ($takimUyesi->rol !== 'danisman') {
            return [
                'granted' => false,
                'reason' => 'Bu bot sadece danışmanlar ve adminler tarafından kullanılabilir.',
                'user' => $user,
            ];
        }

        return [
            'granted' => true,
            'reason' => 'Danışman erişimi onaylandı',
            'user' => $user,
            'takim_uyesi' => $takimUyesi,
            'access_type' => 'danisman',
        ];
    }

    /**
     * Telegram kullanıcısını bul
     */
    protected function findUserByTelegram(array $from): ?User
    {
        $telegramId = $from['id'] ?? null;
        if (! $telegramId) {
            return null;
        }

        return User::where('telegram_chat_id', $telegramId)->first();
    }

    /**
     * Öncelik emoji'si al
     */
    protected function getPriorityEmoji(string $priority): string
    {
        return match ($priority) {
            'acil' => '🚨',
            'yuksek' => '🔴',
            'normal' => '🟡',
            'dusuk' => '🟢',
            default => '⚪'
        };
    }

    /**
     * Durum emoji'si al
     */
    protected function getStatusEmoji(string $status): string
    {
        return match ($status) {
            'bekliyor' => '⏳',
            'devam_ediyor' => '🔄',
            'tamamlandi' => '✅',
            'iptal' => '❌',
            'beklemede' => '⏸️',
            default => '❓'
        };
    }

    /**
     * Admin'lere bildirim gönder
     */
    protected function notifyAdmins(string $message): void
    {
        foreach ($this->adminUsers as $chatId) {
            try {
                $this->sendMessage($chatId, $message);
            } catch (\Exception $e) {
                Log::error("Admin bildirimi gönderilemedi (chat_id: {$chatId}): " . $e->getMessage());
            }
        }
    }

    /**
     * Mesaj gönder
     */
    public function sendMessage(int $chatId, string $text, array $options = []): bool
    {
        try {
            $data = [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
            ];

            if (isset($options['reply_markup'])) {
                $data['reply_markup'] = $options['reply_markup'];
            }

            $response = Http::post("{$this->apiBaseUrl}/sendMessage", $data);

            if ($response->successful()) {
                return true;
            } else {
                Log::error('Telegram mesaj gönderme hatası', [
                    'chat_id' => $chatId,
                    'response' => $response->body(),
                ]);

                return false;
            }
        } catch (\Exception $e) {
            Log::error('Telegram mesaj gönderme exception: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Chat action gönder (typing, upload_voice, find_location, vb.)
     *
     * Context7 Standard: C7-TELEGRAM-TYPING-2025-12-01
     *
     * @param int $chatId
     * @param string $action typing|upload_voice|upload_photo|find_location|record_video|upload_video|record_audio|upload_audio|upload_document|find_location|record_voice|upload_voice
     * @return bool
     */
    public function sendChatAction(int $chatId, string $action = 'typing'): bool
    {
        try {
            $validActions = [
                'typing',
                'upload_photo',
                'record_video',
                'upload_video',
                'record_audio',
                'upload_audio',
                'upload_document',
                'find_location',
                'record_voice',
                'upload_voice',
            ];

            if (!in_array($action, $validActions)) {
                $action = 'typing'; // Varsayılan
            }

            $response = Http::post("{$this->apiBaseUrl}/sendChatAction", [
                'chat_id' => $chatId,
                'action' => $action,
            ]);

            if ($response->successful()) {
                return true;
            } else {
                Log::warning('Telegram chat action gönderme hatası', [
                    'chat_id' => $chatId,
                    'action' => $action,
                    'response' => $response->body(),
                ]);

                return false;
            }
        } catch (\Exception $e) {
            Log::error('Telegram chat action exception: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Bot bilgilerini al
     */
    public function getBotInfo(): array
    {
        try {
            $response = Http::get("{$this->apiBaseUrl}/getMe");

            if ($response->successful()) {
                return $response->json()['result'];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Bot bilgisi alınamadı: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Webhook URL'ini ayarla
     */
    public function setWebhook(string $url): bool
    {
        try {
            $response = Http::post("{$this->apiBaseUrl}/setWebhook", [
                'url' => $url,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Webhook ayarlanamadı: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Webhook bilgilerini al
     */
    public function getWebhookInfo(): array
    {
        try {
            $response = Http::get("{$this->apiBaseUrl}/getWebhookInfo");

            if ($response->successful()) {
                return $response->json()['result'];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Webhook bilgisi alınamadı: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Bot sağlık kontrolü
     */
    public function healthCheck(): array
    {
        $botInfo = $this->getBotInfo();
        $webhookInfo = $this->getWebhookInfo();

        return [
            'bot_connected' => ! empty($botInfo),
            'bot_username' => $botInfo['username'] ?? null,
            'webhook_set' => ! empty($webhookInfo['url']),
            'webhook_url' => $webhookInfo['url'] ?? null,
            'pending_updates' => $webhookInfo['pending_update_count'] ?? 0,
            'last_error' => $webhookInfo['last_error_message'] ?? null,
        ];
    }

    /**
     * Bot ayarlarını al
     */
    public function getSettings(): array
    {
        return [
            'bot_token' => $this->botToken,
            'bot_username' => $this->botUsername,
            'chat_id' => config('services.telegram.chat_id', ''),
            'auto_notifications' => config('services.telegram.auto_notifications', true),
            'task_assignments' => config('services.telegram.task_assignments', true),
            'performance_reports' => config('services.telegram.performance_reports', true),
            'webhook_url' => config('services.telegram.webhook_url', ''),
        ];
    }

    /**
     * Test mesajı gönder
     */
    public function sendTestMessage(string $message): array
    {
        try {
            $chatId = config('services.telegram.chat_id');
            if (empty($chatId)) {
                $chatId = env('TELEGRAM_TEAM_CHANNEL_ID');
            }

            if (! $chatId) {
                return [
                    'success' => false,
                    'message' => 'Chat ID ayarlanmamış',
                ];
            }

            $response = Http::post("{$this->apiBaseUrl}/sendMessage", [
                'chat_id' => $chatId,
                'text' => "🧪 Test Mesajı:\n\n{$message}",
                'text' => "🧪 Test Mesajı:\n\n{$message}",
                'parse_mode' => 'HTML',
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Test mesajı gönderildi',
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Mesaj gönderilemedi: ' . $response->body(),
                ];
            }
        } catch (\Exception $e) {
            Log::error('Test mesajı gönderme hatası: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Ayarları güncelle
     */
    public function updateSettings(array $settings): array
    {
        try {
            // .env dosyasını güncelle
            $envFile = base_path('.env');
            $envContent = file_get_contents($envFile);

            $updates = [];

            if (isset($settings['bot_token'])) {
                $updates['TELEGRAM_BOT_TOKEN'] = $settings['bot_token'];
            }
            if (array_key_exists('bot_username', $settings)) {
                $updates['TELEGRAM_BOT_USERNAME'] = $settings['bot_username'] ?? '';
            }
            if (isset($settings['chat_id'])) {
                $updates['TELEGRAM_CHAT_ID'] = $settings['chat_id'];
            }
            if (array_key_exists('auto_notifications', $settings)) {
                $updates['TELEGRAM_AUTO_NOTIFICATIONS'] = $settings['auto_notifications'] ? 'true' : 'false';
            }
            if (array_key_exists('task_assignments', $settings)) {
                $updates['TELEGRAM_TASK_ASSIGNMENTS'] = $settings['task_assignments'] ? 'true' : 'false';
            }
            if (array_key_exists('performance_reports', $settings)) {
                $updates['TELEGRAM_PERFORMANCE_REPORTS'] = $settings['performance_reports'] ? 'true' : 'false';
            }
            if (isset($settings['telegram_channel_id'])) {
                $updates['TELEGRAM_TEAM_CHANNEL_ID'] = $settings['telegram_channel_id'];
            }

            foreach ($updates as $key => $value) {
                if (strpos($envContent, $key . '=') !== false) {
                    $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
                } else {
                    $envContent .= "\n{$key}={$value}";
                }
            }

            file_put_contents($envFile, $envContent);

            // Cache'i temizle
            Cache::forget('telegram_settings');

            return [
                'success' => true,
                'message' => 'Ayarlar güncellendi',
            ];
        } catch (\Exception $e) {
            Log::error('Telegram ayarları güncelleme hatası: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Bot statusunu al
     */
    public function getStatus(): array
    {
        try {
            $response = Http::get("{$this->apiBaseUrl}/getMe");

            if ($response->successful()) {
                $botInfo = $response->json();

                // Webhook statusunu kontrol et
                $webhookInfo = $this->getWebhookInfo();
                $webhookSet = ! empty($webhookInfo['url'] ?? '');

                // Bekleyen mesaj sayısını al (örnek)
                $pendingMessages = Cache::get('telegram_pending_messages', 0);

                return [
                    'connected' => true,
                    'webhook_set' => $webhookSet,
                    'pending_messages' => $pendingMessages,
                    'bot_info' => $botInfo,
                ];
            } else {
                return [
                    'connected' => false,
                    'webhook_set' => false,
                    'pending_messages' => 0,
                    'error' => 'Bot bağlantısı başarısız',
                ];
            }
        } catch (\Exception $e) {
            Log::error('Bot statusu alma hatası: ' . $e->getMessage());

            return [
                'connected' => false,
                'webhook_set' => false,
                'pending_messages' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Bot testi yap
     */
    public function testBot(): array
    {
        try {
            // Bot bilgilerini al
            $response = Http::get("{$this->apiBaseUrl}/getMe");

            if (! $response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Bot bağlantısı başarısız: ' . $response->body(),
                ];
            }

            $botInfo = $response->json();

            // Test mesajı gönder
            $chatId = config('services.telegram.chat_id');
            if ($chatId) {
                $testResponse = Http::post("{$this->apiBaseUrl}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => "🤖 Bot Testi Başarılı!\n\nBot: @{$botInfo['result']['username']}\nTarih: " . now()->format('Y-m-d H:i:s'),
                    'parse_mode' => 'HTML',
                ]);

                if (! $testResponse->successful()) {
                    return [
                        'success' => false,
                        'message' => 'Test mesajı gönderilemedi: ' . $testResponse->body(),
                    ];
                }
            }

            return [
                'success' => true,
                'message' => 'Bot testi başarılı! Bot status ve çalışıyor.',
                'bot_info' => $botInfo['result'],
            ];
        } catch (\Exception $e) {
            Log::error('Bot testi hatası: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Bot testi sırasında hata: ' . $e->getMessage(),
            ];
        }
    }
}
