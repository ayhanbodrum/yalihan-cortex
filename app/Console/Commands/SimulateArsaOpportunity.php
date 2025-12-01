<?php

namespace App\Console\Commands;

use App\Models\Ilan;
use App\Models\IlanKategori;
use App\Models\Kisi;
use App\Models\Talep;
use App\Models\User;
use App\Services\AI\KisiChurnService;
use App\Services\AI\SmartPropertyMatcherAI;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Simulate Arsa Opportunity Test Command
 *
 * Context7: C7-ARSA-OPPORTUNITY-TEST-2025-11-30
 *
 * Bu komut, Arsa kategorisi için yeni eklenen özelliklerin
 * SmartPropertyMatcherAI tarafından doğru işlendiğini test eder.
 */
class SimulateArsaOpportunity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cortex:test-arsa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Arsa kategorisi için Opportunity Synthesis testi - SmartPropertyMatcherAI reverse matching';

    protected SmartPropertyMatcherAI $propertyMatcher;
    protected KisiChurnService $churnService;

    /**
     * Create a new command instance.
     */
    public function __construct(SmartPropertyMatcherAI $propertyMatcher, KisiChurnService $churnService)
    {
        parent::__construct();
        $this->propertyMatcher = $propertyMatcher;
        $this->churnService = $churnService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🎯 Arsa Opportunity Simulation Test Başlatılıyor...');
        $this->newLine();

        DB::beginTransaction();

        try {
            // 1. Test Verilerini Oluştur
            $testData = $this->createTestData();

            // 2. SmartPropertyMatcherAI ile Test Et
            $results = $this->runMatchingTest($testData);

            // 3. Sonuçları Göster
            $this->displayResults($results, $testData);

            // 4. n8n Payload'ını Göster
            $this->displayN8nPayload($results, $testData);

            // Test verilerini geri al (transaction rollback)
            DB::rollBack();

            $this->newLine();
            $this->info('✅ Test başarıyla tamamlandı! (Test verileri geri alındı)');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();

            $this->error('❌ Test başarısız: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());

            return Command::FAILURE;
        }
    }

    /**
     * Test verilerini oluştur
     */
    private function createTestData(): array
    {
        $this->info('📦 Test verileri oluşturuluyor...');

        // 1. Arsa kategorisini bul
        $arsaKategori = IlanKategori::where('slug', 'arsa')
            ->orWhere('slug', 'imar-arsalari')
            ->first();

        if (! $arsaKategori) {
            // Eğer kategori yoksa, varsayılan bir kategori oluştur
            $arsaKategori = IlanKategori::first();
            if (! $arsaKategori) {
                throw new \Exception('Arsa kategorisi bulunamadı ve yedek kategori de yok!');
            }
        }

        // 2. İmar Arsaları alt kategorisini bul
        $altKategori = IlanKategori::where('parent_id', $arsaKategori->id)
            ->where(function ($q) {
                $q->where('slug', 'imar-arsalari')
                    ->orWhere('slug', 'like', '%imar%');
            })
            ->first() ?? $arsaKategori; // Fallback

        // 3. Test Danışmanı bul veya oluştur
        $danisman = User::first();
        if (! $danisman) {
            throw new \Exception('Test için en az bir kullanıcı (danışman) gereklidir!');
        }

        // 4. Test Müşterisi oluştur (Churn Risk yüksek olacak şekilde)
        // Observer'ı devre dışı bırak (test ortamında auth() null dönebilir)
        $kisi = Kisi::withoutEvents(function () use ($danisman) {
            return Kisi::create([
                'ad' => 'Test Müşteri',
                'soyad' => 'Churn Risk Yüksek',
                'telefon' => '555' . rand(1000000, 9999999),
                'email' => 'test-churn-' . time() . '@example.com',
                'status' => true,
                'created_at' => now()->subDays(30), // 30 gün önce oluşturuldu
            ]);
        });

        // 5. Sanal İlan Oluştur (DB'ye kaydediyoruz ama transaction içinde)
        // İstanbul/Muğla gibi bir il ve ilçe seçelim (test için)
        $testIl = \App\Models\Il::first();
        if (!$testIl) {
            // Eğer il yoksa, test için bir il oluşturalım
            $testIl = \App\Models\Il::create([
                'il_adi' => 'Test İl',
                'status' => true,
            ]);
        }

        $testIlce = $testIl ? \App\Models\Ilce::where('il_id', $testIl->id)->first() : null;
        if (!$testIlce && $testIl) {
            // Eğer ilçe yoksa, test için bir ilçe oluşturalım
            $testIlce = \App\Models\Ilce::create([
                'il_id' => $testIl->id,
                'ilce_adi' => 'Test İlçe',
                'status' => true,
            ]);
        }

        $ilan = Ilan::create([
            'baslik' => 'Test Arsa İlanı - Ticari İmarlı',
            'aciklama' => 'Test için oluşturulmuş arsa ilanı',
            'fiyat' => 5000000,
            'para_birimi' => 'TRY',
            'status' => 1, // Aktif status
            'ana_kategori_id' => $arsaKategori->id,
            'alt_kategori_id' => $altKategori->id,
            'danisman_id' => $danisman->id,
            'imar_statusu' => 'Ticari İmarlı', // Config'deki label ile aynı olmalı
            'ada_no' => '1453',
            'parsel_no' => '1',
            'kaks' => 2.0,
            'taks' => 0.5,
            'alan_m2' => 1000,
            'il_id' => $testIl ? $testIl->id : null,
            'ilce_id' => $testIlce ? $testIlce->id : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // İlan'a Feature özelliği ekle (SmartPropertyMatcherAI için)
        // calculateFeatureScore metodu $ilan->ozellikler->pluck('slug') kullanıyor
        // Slug formatı: imar-durumu veya imar-statusu (seeder'larda görülen format)
        $imarStatusuFeature = \App\Models\Feature::firstOrCreate(
            ['slug' => 'imar-durumu'], // Slug formatında (tire ile)
            [
                'name' => 'İmar Durumu',
                'type' => 'select',
                'status' => true,
                'display_order' => 1,
            ]
        );

        // İlan'a özelliği ilişkilendir (pivot tablo: ilan_feature)
        $ilan->ozellikler()->syncWithoutDetaching([
            $imarStatusuFeature->id => ['value' => 'Ticari İmarlı'],
        ]);

        // 6. Sanal Talep Oluştur (Churn Risk Yüksek)
        // Aynı konum bilgilerini ekleyelim (eşleşme için)
        $talep = Talep::create([
            'baslik' => 'Ticari İmarlı Arsa Arayışı',
            'aciklama' => 'Ticari imarlı arsa arıyorum',
            'status' => 1, // Talep status boolean (1 = Aktif)
            'kisi_id' => $kisi->id,
            'danisman_id' => $danisman->id,
            'alt_kategori_id' => $altKategori->id,
            'il_id' => $testIl ? $testIl->id : null,
            'ilce_id' => $testIlce ? $testIlce->id : null,
            'min_fiyat' => 4000000,
            'max_fiyat' => 6000000,
            'aranan_ozellikler_json' => [
                'imar-durumu', // Slug formatında (SmartPropertyMatcherAI slug bekliyor)
                'kaks', // Slug formatında
            ],
            'created_at' => now()->subDays(25), // 25 gün önce oluşturuldu (churn risk)
            'updated_at' => now()->subDays(25),
        ]);

        // 7. Kisi'yi Churn Risk yüksek yapmak için son etkileşim oluştur
        // (KisiChurnService etkilesimler() relationship'ini kontrol eder)
        \App\Models\KisiEtkilesim::create([
            'kisi_id' => $kisi->id,
            'kullanici_id' => $danisman->id,
            'tip' => 'telefon',
            'notlar' => 'Test etkileşimi - Churn risk yüksek müşteri',
            'etkilesim_tarihi' => now()->subDays(30), // 30 gün önce (churn risk için yeterli)
            'status' => 1,
        ]);

        // Kisi'nin segment'ini "soğuk" yap (churn risk artırır)
        $kisi->segment = 'soğuk';
        $kisi->pipeline_stage = 1; // Geride (churn risk artırır)
        $kisi->save();

        $this->info("✅ Test verileri oluşturuldu:");
        $this->line("   - İlan ID: {$ilan->id}");
        $this->line("   - Talep ID: {$talep->id}");
        $this->line("   - Kişi ID: {$kisi->id}");

        return [
            'ilan' => $ilan,
            'talep' => $talep,
            'kisi' => $kisi,
            'danisman' => $danisman,
            'arsa_kategori' => $arsaKategori,
            'alt_kategori' => $altKategori,
        ];
    }

    /**
     * Matching testini çalıştır
     */
    private function runMatchingTest(array $testData): array
    {
        $this->newLine();
        $this->info('🔍 SmartPropertyMatcherAI reverse matching test ediliyor...');

        $ilan = $testData['ilan'];
        $talep = $testData['talep'];
        $kisi = $testData['kisi'];

        // 1. Churn Risk Analizi
        $churnRisk = $this->churnService->calculateChurnRisk($kisi);

        // 2. Reverse Matching (İlan için uygun talepleri bul)
        // Debug: Hard filter öncesi kontrol
        $this->line("   🔍 Debug bilgileri:");
        $this->line("      - İlan ID: {$ilan->id}, Alt Kategori ID: {$ilan->alt_kategori_id}, Fiyat: {$ilan->fiyat}, İlçe ID: {$ilan->ilce_id}");
        $this->line("      - Talep ID: {$talep->id}, Alt Kategori ID: {$talep->alt_kategori_id}, Min: {$talep->min_fiyat}, Max: {$talep->max_fiyat}, İlçe ID: {$talep->ilce_id}");

        // Debug: İlan özelliklerini kontrol et
        $ilan->load('ozellikler');
        $ilanOzellikleri = $ilan->ozellikler->pluck('slug')->toArray();
        $this->line("      - İlan özellikleri (slug): " . implode(', ', $ilanOzellikleri ?: ['Yok']));
        $talepArananOzellikler = is_array($talep->aranan_ozellikler_json) ? $talep->aranan_ozellikler_json : [];
        $this->line("      - Talep aranan özellikler: " . implode(', ', $talepArananOzellikler ?: ['Yok']));

        // Debug: Hard filter'ı manuel test et
        $manualFilterTest = Talep::query()
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->where('alt_kategori_id', $ilan->alt_kategori_id)
            ->where(function ($q) use ($ilan) {
                $minPriceWithFlex = $ilan->fiyat * 0.8;
                $maxPriceWithFlex = $ilan->fiyat * 1.2;
                $q->where(function ($subQ) use ($maxPriceWithFlex) {
                    $subQ->whereNull('min_fiyat')
                        ->orWhere('min_fiyat', '<=', $maxPriceWithFlex);
                })
                    ->where(function ($subQ) use ($minPriceWithFlex) {
                        $subQ->whereNull('max_fiyat')
                            ->orWhere('max_fiyat', '>=', $minPriceWithFlex);
                    });
            })
            ->get();

        $this->line("      - Hard filter sonrası bulunan talep sayısı: {$manualFilterTest->count()}");
        $manualIds = $manualFilterTest->pluck('id')->toArray();
        $this->line("      - Bulunan talep ID'leri: " . implode(', ', $manualIds ?: ['Yok']));

        $matches = $this->propertyMatcher->reverseMatch($ilan);

        // Debug: Eşleşen talep ID'leri
        $matchedIds = collect($matches)->pluck('talep.id')->toArray();
        $this->line("      - Eşleşen talep ID'leri (80+ puan): " . implode(', ', $matchedIds ?: ['Yok']));

        // Debug: Eğer hard filter'dan geçti ama 80+ puan yoksa, tüm puanları göster
        if ($manualFilterTest->count() > 0 && count($matches) === 0) {
            $this->line("      ⚠️  Hard filter'dan geçti ama 80+ puan yok. Puanları kontrol edin.");
        }

        // 3. Eşleşmeleri zenginleştir (Churn Risk ile)
        $enrichedMatches = [];

        foreach ($matches as $match) {
            if ($match['talep']->id === $talep->id) {
                $matchScore = $match['score'];
                $churnScore = $churnRisk['score'];

                // Action Score = Match Score + (Churn Score * 0.5)
                $actionScore = $matchScore + ($churnScore * 0.5);

                $enrichedMatches[] = [
                    'talep_id' => $match['talep']->id,
                    'talep_baslik' => $match['talep']->baslik,
                    'kisi_id' => $match['talep']->kisi_id,
                    'match_score' => $matchScore,
                    'churn_score' => $churnScore,
                    'action_score' => $actionScore,
                    'reasons' => $match['reasons'] ?? [],
                    'breakdown' => $match['breakdown'] ?? [],
                ];
            }
        }

        return [
            'matches' => $enrichedMatches,
            'churn_risk' => $churnRisk,
            'total_matches' => count($matches),
        ];
    }

    /**
     * Sonuçları göster
     */
    private function displayResults(array $results, array $testData): void
    {
        $this->newLine();
        $this->info('📊 TEST SONUÇLARI');
        $this->info(str_repeat('=', 80));

        // Churn Risk
        $churnRisk = $results['churn_risk'];
        $this->newLine();
        $this->info('🔴 Churn Risk Analizi:');
        $this->line("   Risk Skoru: {$churnRisk['score']}/100");
        $this->line("   Risk Seviyesi: {$this->getRiskLevel($churnRisk['score'])}");

        if (isset($churnRisk['breakdown'])) {
            $this->line("   Detaylar:");
            foreach ($churnRisk['breakdown'] as $key => $value) {
                $this->line("     - {$key}: {$value}");
            }
        }

        // Matches
        $matches = $results['matches'];
        $this->newLine();
        $this->info("🎯 Eşleşme Sonuçları ({$results['total_matches']} talep bulundu):");

        if (empty($matches)) {
            $this->warn('   ⚠️  Beklenen talep eşleşmedi! (Talep ID: ' . $testData['talep']->id . ')');
            $this->line('   Bu durumda şunları kontrol edin:');
            $this->line('     1. Kategori eşleşmesi (alt_kategori_id)');
            $this->line('     2. Fiyat aralığı (%20 esneme payı)');
            $this->line('     3. Status (Aktif)');
        } else {
            foreach ($matches as $match) {
                $this->newLine();
                $this->info("   📋 Talep #{$match['talep_id']}: {$match['talep_baslik']}");

                // Console Table
                $table = [
                    ['Metric', 'Değer', 'Durum'],
                    ['Match Skoru', $match['match_score'] . '/100', $this->getScoreStatus($match['match_score'])],
                    ['Churn Skoru', $match['churn_score'] . '/100', $this->getRiskLevel($match['churn_score'])],
                    ['Action Skoru', round($match['action_score'], 2) . '/100', $this->getActionScoreStatus($match['action_score'])],
                ];

                $this->table(['Metric', 'Değer', 'Durum'], array_slice($table, 1));

                // Breakdown
                if (isset($match['breakdown'])) {
                    $this->line('   Breakdown:');
                    $this->line("     - Konum: {$match['breakdown']['location']}/40");
                    $this->line("     - Fiyat: {$match['breakdown']['price']}/30");
                    $this->line("     - Özellikler: {$match['breakdown']['features']}/30");
                }

                // Reasons
                if (! empty($match['reasons'])) {
                    $this->line('   Nedenler:');
                    foreach ($match['reasons'] as $reason) {
                        $this->line("     ✅ {$reason}");
                    }
                }

                // Beklenen çıktı kontrolü
                $this->newLine();
                if ($match['match_score'] >= 85) {
                    $this->info('   ✅ BAŞARILI: Match skoru 85\'in üzerinde veya eşit!');
                } else {
                    $this->warn("   ⚠️  UYARI: Match skoru 85'in altında ({$match['match_score']})");
                }

                if ($match['action_score'] > 85) {
                    $this->info('   ✅ BAŞARILI: Action skoru 85\'in üzerinde!');
                } else {
                    $this->warn("   ⚠️  UYARI: Action skoru 85'in altında ({$match['action_score']})");
                }
            }
        }
    }

    /**
     * n8n Payload'ını göster
     */
    private function displayN8nPayload(array $results, array $testData): void
    {
        $this->newLine();
        $this->info('📤 n8n Webhook Payload (Opportunity Synthesis):');
        $this->info(str_repeat('=', 80));

        $matches = $results['matches'];

        if (empty($matches)) {
            $this->warn('   ⚠️  Eşleşme bulunamadı, payload oluşturulamadı.');
            return;
        }

        foreach ($matches as $match) {
            $payload = [
                'event' => 'opportunity_synthesis',
                'event_type' => 'arsa_opportunity',
                'priority' => $match['action_score'] > 85 ? 'high' : 'medium',
                'ilan' => [
                    'id' => $testData['ilan']->id,
                    'baslik' => $testData['ilan']->baslik,
                    'fiyat' => $testData['ilan']->fiyat,
                    'para_birimi' => $testData['ilan']->para_birimi,
                    'imar_statusu' => $testData['ilan']->imar_statusu,
                    'ada_no' => $testData['ilan']->ada_no,
                    'parsel_no' => $testData['ilan']->parsel_no,
                    'kaks' => $testData['ilan']->kaks,
                ],
                'talep' => [
                    'id' => $match['talep_id'],
                    'baslik' => $match['talep_baslik'],
                    'kisi_id' => $match['kisi_id'],
                ],
                'scores' => [
                    'match_score' => $match['match_score'],
                    'churn_score' => $match['churn_score'],
                    'action_score' => round($match['action_score'], 2),
                ],
                'reasons' => $match['reasons'],
                'churn_analysis' => [
                    'risk_score' => $results['churn_risk']['score'],
                    'risk_level' => $this->getRiskLevel($results['churn_risk']['score']),
                    'breakdown' => $results['churn_risk']['breakdown'] ?? [],
                ],
                'notification_channels' => ['telegram', 'whatsapp', 'email'],
                'action_items' => [
                    'Hemen telefon et',
                    'Özel teklif hazırla',
                    'VIP muamele göster',
                ],
                'timestamp' => now()->toISOString(),
            ];

            $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->newLine();
        }
    }

    /**
     * Risk seviyesini belirle
     */
    private function getRiskLevel(int $score): string
    {
        if ($score >= 70) {
            return '🔴 Yüksek Risk';
        } elseif ($score >= 40) {
            return '🟡 Orta Risk';
        } else {
            return '🟢 Düşük Risk';
        }
    }

    /**
     * Skor durumunu belirle
     */
    private function getScoreStatus(float $score): string
    {
        if ($score >= 85) {
            return '✅ Mükemmel';
        } elseif ($score >= 70) {
            return '🟡 İyi';
        } elseif ($score >= 50) {
            return '🟠 Orta';
        } else {
            return '🔴 Düşük';
        }
    }

    /**
     * Action skor durumunu belirle
     */
    private function getActionScoreStatus(float $score): string
    {
        if ($score >= 85) {
            return '🔥 ACİL FIRSAT';
        } elseif ($score >= 70) {
            return '⭐ Öncelikli';
        } elseif ($score >= 50) {
            return '📋 Normal';
        } else {
            return '⏸️  Düşük Öncelik';
        }
    }
}
