<?php

namespace Database\Seeders;

use App\Enums\KisiStatus;
use App\Enums\YatirimciProfili;
use App\Models\Kisi;
use Illuminate\Database\Seeder;

/**
 * Update Kisiler with AI Data Seeder
 *
 * Context7: Test verisi oluşturma - Müşteri listesi UI geliştirmelerini test etmek için
 * AI alanlarını (satis_potansiyeli, yatirimci_profili, aciliyet_derecesi, crm_status) rastgele değerlerle doldurur.
 */
class UpdateKisilerWithAiDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🤖 Müşteri AI verileri güncelleniyor...');

        // Tüm kişileri çek
        $kisiler = Kisi::all();

        if ($kisiler->isEmpty()) {
            $this->command->warn('⚠️  Hiç müşteri bulunamadı. Önce müşteri ekleyin.');

            return;
        }

        $this->command->info("📊 Toplam {$kisiler->count()} müşteri bulundu.");

        // Yatırımcı profili seçenekleri (Kullanıcı isteği: Fırsatçı, Uzun Vadeli, Muhafazakar, VIP, Yabancı Yatırımcı)
        // Enum mapping: Fırsatçı->FIRSATCI, Muhafazakar->KONSERVATIF, Uzun Vadeli->DENGE, VIP/Yabancı->AGRESIF
        $yatirimciProfilleri = [
            YatirimciProfili::FIRSATCI,      // Fırsatçı
            YatirimciProfili::DENGE,          // Uzun Vadeli
            YatirimciProfili::KONSERVATIF,    // Muhafazakar
            YatirimciProfili::AGRESIF,        // VIP / Yabancı Yatırımcı
            YatirimciProfili::AGRESIF,        // VIP / Yabancı Yatırımcı (daha fazla çeşitlilik için)
        ];

        // Aciliyet derecesi seçenekleri (Kullanıcı isteği: Yüksek, Orta, Düşük)
        $aciliyetDereceleri = ['yuksek', 'orta', 'dusuk'];

        // CRM Status seçenekleri (Kullanıcı isteği: Sıcak, Takipte, Soğuk, Müşteri)
        $crmStatuslar = [
            KisiStatus::SICAK,     // Sıcak
            KisiStatus::TAKIPTE,   // Takipte
            KisiStatus::SOGUK,     // Soğuk
            KisiStatus::MUSTERI,   // Müşteri
        ];

        $updated = 0;
        $skipped = 0;

        foreach ($kisiler as $kisi) {
            try {
                // Satış potansiyeli: 10-99 arası rastgele
                $satisPotansiyeli = rand(10, 99);

                // Yatırımcı profili: Rastgele seç
                $yatirimciProfili = $yatirimciProfilleri[array_rand($yatirimciProfilleri)];

                // Aciliyet derecesi: Rastgele seç
                $aciliyetDerecesi = $aciliyetDereceleri[array_rand($aciliyetDereceleri)];

                // CRM Status: Rastgele seç
                $crmStatus = $crmStatuslar[array_rand($crmStatuslar)];

                // Karar verici mi: %70 ihtimalle true
                $kararVericiMi = rand(1, 100) <= 70;

                // Güncelle
                $kisi->update([
                    'satis_potansiyeli' => $satisPotansiyeli,
                    'yatirimci_profili' => $yatirimciProfili->value,
                    'aciliyet_derecesi' => $aciliyetDerecesi,
                    'crm_status' => $crmStatus->value,
                    'karar_verici_mi' => $kararVericiMi,
                ]);

                $updated++;

                // Her 50 kayıtta bir progress göster
                if ($updated % 50 === 0) {
                    $this->command->info("   ✓ {$updated} müşteri güncellendi...");
                }
            } catch (\Exception $e) {
                $skipped++;
                $this->command->warn("   ⚠️  Müşteri ID {$kisi->id} güncellenemedi: ".$e->getMessage());
            }
        }

        $this->command->newLine();
        $this->command->info('✅ İşlem tamamlandı!');
        $this->command->info("   📈 Güncellenen: {$updated} müşteri");
        if ($skipped > 0) {
            $this->command->warn("   ⚠️  Atlanan: {$skipped} müşteri");
        }

        // Özet istatistikler
        $this->command->newLine();
        $this->command->info('📊 Özet İstatistikler:');

        $stats = [
            'Ortalama Satış Potansiyeli' => Kisi::whereNotNull('satis_potansiyeli')->avg('satis_potansiyeli'),
            'Yüksek Potansiyel (>80)' => Kisi::where('satis_potansiyeli', '>', 80)->count(),
            'Orta Potansiyel (50-80)' => Kisi::whereBetween('satis_potansiyeli', [50, 80])->count(),
            'Düşük Potansiyel (<50)' => Kisi::where('satis_potansiyeli', '<', 50)->count(),
        ];

        foreach ($stats as $label => $value) {
            if ($value !== null) {
                $this->command->line("   • {$label}: ".(is_float($value) ? number_format($value, 1) : $value));
            }
        }

        // Yatırımcı profili dağılımı
        $this->command->newLine();
        $this->command->info('💼 Yatırımcı Profili Dağılımı:');
        foreach ($yatirimciProfilleri as $profil) {
            $count = Kisi::where('yatirimci_profili', $profil->value)->count();
            $this->command->line("   • {$profil->label()}: {$count}");
        }

        // CRM Status dağılımı
        $this->command->newLine();
        $this->command->info('📊 CRM Status Dağılımı:');
        foreach ($crmStatuslar as $status) {
            $count = Kisi::where('crm_status', $status->value)->count();
            $this->command->line("   • {$status->label()}: {$count}");
        }
    }
}
