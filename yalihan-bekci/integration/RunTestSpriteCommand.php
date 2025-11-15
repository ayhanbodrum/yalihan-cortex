<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MCP\TestSpriteService;

class RunTestSpriteCommand extends Command
{
    /**
     * Komut adı
     *
     * @var string
     */
    protected $signature = 'testsprite:run {--type=all : Test türü (all, migrations, seeders)} {--report=summary : Rapor türü (summary, detailed, changelog)}';

    /**
     * Komut açıklaması
     *
     * @var string
     */
    protected $description = 'TestSprite MCP testlerini çalıştır';

    /**
     * TestSprite servisi
     *
     * @var \App\Services\MCP\TestSpriteService
     */
    protected $testSpriteService;

    /**
     * Komut oluşturucu
     *
     * @param \App\Services\MCP\TestSpriteService $testSpriteService
     * @return void
     */
    public function __construct(TestSpriteService $testSpriteService)
    {
        parent::__construct();
        $this->testSpriteService = $testSpriteService;
    }

    /**
     * Komutu çalıştır
     *
     * @return int
     */
    public function handle()
    {
        $type = $this->option('type');
        $reportType = $this->option('report');

        $this->info('TestSprite MCP testleri başlatılıyor...');

        // MCP sunucusunun çalıştığını kontrol et
        if (!$this->testSpriteService->isServerRunning()) {
            $this->warn('TestSprite MCP sunucusu çalışmıyor. Başlatılıyor...');

            if (!$this->testSpriteService->startServer()) {
                $this->error('TestSprite MCP sunucusu başlatılamadı!');
                return 1;
            }

            $this->info('TestSprite MCP sunucusu başlatıldı.');
        }

        // Test seçeneklerini hazırla
        $options = [];
        if ($type !== 'all') {
            $options['type'] = $type;
        }

        // Testleri çalıştır
        $this->info('Testler çalıştırılıyor...');
        $results = $this->testSpriteService->runTests($options);

        if (isset($results['error'])) {
            $this->error($results['message']);
            return 1;
        }

        // Sonuçları göster
        $this->info('Test sonuçları:');
        $this->table(
            ['Toplam Test', 'Başarılı', 'Başarısız', 'Başarı Oranı'],
            [[
                $results['summary']['totalTests'],
                $results['summary']['passedTests'],
                $results['summary']['failedTests'],
                $results['summary']['successRate']
            ]]
        );

        // Rapor oluştur
        $this->info("'{$reportType}' türünde rapor oluşturuluyor...");
        $report = $this->testSpriteService->generateReport($reportType);

        if (isset($report['error'])) {
            $this->error($report['message']);
        } else {
            $this->info("Rapor oluşturuldu: {$report['path']}");

            // Başarısız testler varsa uyarı göster
            if ($results['summary']['failedTests'] > 0) {
                $this->warn("Dikkat: {$results['summary']['failedTests']} test başarısız oldu!");
                $this->warn("Detaylı bilgi için raporu inceleyin: {$report['path']}");
                return 1;
            }
        }

        $this->info('TestSprite MCP testleri tamamlandı.');
        return 0;
    }
}
