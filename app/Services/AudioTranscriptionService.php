<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * AudioTranscriptionService
 *
 * Context7 Standard: C7-AUDIO-TRANSCRIPTION-2025-12-01
 *
 * Whisper API ile ses dosyalarını yazıya çevirir.
 * Docker üzerinde çalışan yerel Whisper servisi kullanılır.
 */
class AudioTranscriptionService
{
    private string $whisperUrl;
    private int $timeout;

    public function __construct()
    {
        // .env'den WHISPER_URL al, varsayılan: http://whisper:9000
        $this->whisperUrl = env('WHISPER_URL', 'http://whisper:9000');
        $this->timeout = (int) env('WHISPER_TIMEOUT', 60); // Varsayılan 60 saniye

        // Settings tablosundan override etme (eğer varsa)
        try {
            if (class_exists(\App\Models\Setting::class)) {
                $settings = \App\Models\Setting::query()
                    ->whereIn('key', ['ai_whisper_url', 'ai_whisper_timeout'])
                    ->pluck('value', 'key');

                $this->whisperUrl = (string) ($settings['ai_whisper_url'] ?? $this->whisperUrl);
                $this->timeout = (int) ($settings['ai_whisper_timeout'] ?? $this->timeout);
            }
        } catch (\Throwable $e) {
            Log::notice('AudioTranscriptionService settings override skipped', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Ses dosyasını yazıya çevir
     *
     * @param string $localFilePath Yerel dosya yolu (storage/app içinde)
     * @return string Transkript edilmiş metin
     * @throws \RuntimeException
     */
    public function transcribe(string $localFilePath): string
    {
        if (!Storage::exists($localFilePath)) {
            throw new \RuntimeException("Ses dosyası bulunamadı: {$localFilePath}");
        }

        $fullPath = Storage::path($localFilePath);

        try {
            // Laravel'in yerleşik retry mekanizması: 3 deneme, 1 saniye bekleme
            $response = Http::retry(3, 1000, function ($exception, $request) {
                // ConnectionException (ağ hatası) durumunda retry yap
                if ($exception instanceof ConnectionException) {
                    return true;
                }

                // RequestException durumunda status code'a bak
                if ($exception instanceof RequestException) {
                    $statusCode = $exception->response?->status();
                    // 5xx hatalarında retry yap, 4xx hatalarında yapma
                    return $statusCode >= 500;
                }

                // Diğer exception'larda retry yap
                return true;
            })
                ->timeout($this->timeout)
                ->attach('audio_file', file_get_contents($fullPath), basename($fullPath))
                ->post(rtrim($this->whisperUrl, '/') . '/asr', [
                    'task' => 'transcribe',
                    'language' => 'tr',
                    'output' => 'json',
                ])
                ->throw(); // Hata durumunda exception fırlat

            $responseData = $response->json();

            // Whisper API response formatı: {"text": "transkript edilmiş metin"}
            $transcript = $responseData['text'] ?? '';

            if (empty($transcript)) {
                Log::warning('AudioTranscriptionService: Boş transkript döndü', [
                    'file' => $localFilePath,
                    'response' => $responseData,
                ]);
                throw new \RuntimeException('Whisper API boş transkript döndü');
            }

            Log::info('AudioTranscriptionService: Transkript başarılı', [
                'file' => $localFilePath,
                'transcript_length' => strlen($transcript),
            ]);

            return trim($transcript);
        } catch (ConnectionException $e) {
            Log::error('AudioTranscriptionService: Whisper servisine bağlanılamadı', [
                'url' => $this->whisperUrl,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Whisper servisine bağlanılamadı (Offline)');
        } catch (RequestException $e) {
            $statusCode = $e->response?->status();
            Log::error('AudioTranscriptionService: Whisper API hatası', [
                'url' => $this->whisperUrl,
                'status' => $statusCode,
                'error' => $e->getMessage(),
            ]);

            if ($statusCode >= 400 && $statusCode < 500) {
                throw new \RuntimeException('Whisper API client hatası: ' . $e->getMessage());
            }

            throw new \RuntimeException('Whisper API server hatası: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('AudioTranscriptionService: Beklenmeyen hata', [
                'url' => $this->whisperUrl,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \RuntimeException('Ses transkripsiyonu başarısız: ' . $e->getMessage());
        }
    }

    /**
     * Telegram'dan gelen voice dosyasını indir ve yerel dosya yolunu döndür
     *
     * @param string $fileId Telegram file_id
     * @param string $botToken Telegram bot token
     * @return string Yerel dosya yolu (storage/app içinde)
     * @throws \RuntimeException
     */
    public function downloadTelegramVoice(string $fileId, string $botToken): string
    {
        try {
            // 1. File path al
            $filePathResponse = Http::timeout(10)
                ->get("https://api.telegram.org/bot{$botToken}/getFile", [
                    'file_id' => $fileId,
                ])
                ->throw();

            $filePathData = $filePathResponse->json();
            $telegramFilePath = $filePathData['result']['file_path'] ?? null;

            if (!$telegramFilePath) {
                throw new \RuntimeException('Telegram file path alınamadı');
            }

            // 2. Dosyayı indir
            $fileUrl = "https://api.telegram.org/file/bot{$botToken}/{$telegramFilePath}";
            $fileContent = Http::timeout(30)
                ->get($fileUrl)
                ->throw()
                ->body();

            // 3. Geçici dosya olarak kaydet (storage/app/temp_audio/)
            $localFileName = 'telegram_voice_' . uniqid() . '_' . time() . '.ogg';
            $localFilePath = "temp_audio/{$localFileName}";

            Storage::put($localFilePath, $fileContent);

            Log::info('AudioTranscriptionService: Telegram voice dosyası indirildi', [
                'file_id' => $fileId,
                'local_path' => $localFilePath,
                'size' => strlen($fileContent),
            ]);

            return $localFilePath;
        } catch (\Exception $e) {
            Log::error('AudioTranscriptionService: Telegram voice indirme hatası', [
                'file_id' => $fileId,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Telegram voice dosyası indirilemedi: ' . $e->getMessage());
        }
    }

    /**
     * Geçici ses dosyasını sil
     *
     * @param string $localFilePath
     * @return void
     */
    public function cleanup(string $localFilePath): void
    {
        try {
            if (Storage::exists($localFilePath)) {
                Storage::delete($localFilePath);
                Log::debug('AudioTranscriptionService: Geçici dosya silindi', [
                    'file' => $localFilePath,
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('AudioTranscriptionService: Dosya silme hatası', [
                'file' => $localFilePath,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
