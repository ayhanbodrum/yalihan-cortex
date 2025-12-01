<?php

namespace App\Services\MCP;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestSpriteService
{
    /**
     * MCP sunucu URL'si
     *
     * @var string
     */
    protected $serverUrl;

    /**
     * Yapılandırma
     *
     * @var array
     */
    protected $config;

    /**
     * TestSpriteService constructor.
     */
    public function __construct()
    {
        $this->config = config('testsprite');
        $this->serverUrl = $this->config['server_url'] ?? 'http://localhost:3333';
    }

    /**
     * Testleri çalıştır
     *
     * @param  array  $options  Test seçenekleri
     * @return array Test sonuçları
     */
    public function runTests(array $options = [])
    {
        try {
            $response = Http::post("{$this->serverUrl}/run-tests", $options);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('TestSprite MCP sunucusu hata döndürdü', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'error' => true,
                'message' => 'TestSprite MCP sunucusu hata döndürdü: '.$response->status(),
            ];
        } catch (Exception $e) {
            Log::error('TestSprite MCP sunucusuna bağlanırken hata oluştu', [
                'exception' => $e->getMessage(),
            ]);

            return [
                'error' => true,
                'message' => 'TestSprite MCP sunucusuna bağlanırken hata oluştu: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Bilgi tabanında arama yap
     *
     * @param  string  $query  Arama sorgusu
     * @return array Arama sonuçları
     */
    public function search(string $query)
    {
        try {
            $response = Http::get("{$this->serverUrl}/knowledge", [
                'q' => $query,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'error' => true,
                'message' => 'Arama yapılırken hata oluştu: '.$response->status(),
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => 'Arama yapılırken hata oluştu: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Rapor oluştur
     *
     * @param  string  $type  Rapor türü (summary, detailed, changelog)
     * @return array Rapor bilgileri
     */
    public function generateReport(string $type = 'summary')
    {
        try {
            $response = Http::get("{$this->serverUrl}/reports", [
                'type' => $type,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'error' => true,
                'message' => 'Rapor oluşturulurken hata oluştu: '.$response->status(),
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => 'Rapor oluşturulurken hata oluştu: '.$e->getMessage(),
            ];
        }
    }

    /**
     * MCP sunucusunun durumunu kontrol et
     *
     * @return bool Sunucu çalışıyorsa true, çalışmıyorsa false
     */
    public function isServerRunning()
    {
        try {
            $response = Http::get($this->serverUrl);

            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * MCP sunucusunu başlat
     *
     * @return bool Başlatma başarılıysa true, değilse false
     */
    public function startServer()
    {
        $nodePath = $this->config['node_path'] ?? 'node';
        $serverPath = base_path('testsprite/server/index.js');

        if (! file_exists($serverPath)) {
            Log::error('TestSprite MCP sunucu dosyası bulunamadı', [
                'path' => $serverPath,
            ]);

            return false;
        }

        try {
            $command = "{$nodePath} {$serverPath} > ".storage_path('logs/testsprite.log').' 2>&1 &';
            exec($command);

            // Sunucunun başlaması için biraz bekle
            sleep(2);

            return $this->isServerRunning();
        } catch (Exception $e) {
            Log::error('TestSprite MCP sunucusu başlatılırken hata oluştu', [
                'exception' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
