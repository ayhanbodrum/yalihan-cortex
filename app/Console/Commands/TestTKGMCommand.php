<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TKGMService;

class TestTKGMCommand extends Command
{
    protected $signature = 'tkgm:test {ada} {parsel} {il} {ilce}';
    protected $description = 'TKGM parsel sorgulama testi';

    public function handle()
    {
        $ada = $this->argument('ada');
        $parsel = $this->argument('parsel');
        $il = $this->argument('il');
        $ilce = $this->argument('ilce');

        $this->info("TKGM Parsel Sorgulama Testi");
        $this->info("============================");
        $this->info("Ada: {$ada}, Parsel: {$parsel}, İl: {$il}, İlçe: {$ilce}");
        $this->newLine();

        $service = app(TKGMService::class);
        $result = $service->parselSorgula($ada, $parsel, $il, $ilce);

        if ($result['success']) {
            $this->info("✅ Başarılı!");
            $this->newLine();

            $pb = $result['parsel_bilgileri'];
            $this->line("İl: " . ($pb['il'] ?? 'N/A'));
            $this->line("İlçe: " . ($pb['ilce'] ?? 'N/A'));
            $this->line("Mahalle/Köy: " . ($pb['mahalle'] ?? 'N/A'));
            $this->line("Mahalle No: " . ($pb['mahalle_no'] ?? 'N/A'));
            $this->line("Ada: " . ($pb['ada'] ?? 'N/A'));
            $this->line("Parsel: " . ($pb['parsel'] ?? 'N/A'));
            $this->line("Tapu Alanı: " . ($pb['tapu_alani'] ?? $pb['yuzolcumu'] . ' m²'));
            $this->line("Nitelik: " . ($pb['nitelik'] ?? 'N/A'));
            $this->line("Mevkii: " . ($pb['mevkii'] ?? 'N/A'));
            $this->line("Zemin Tip: " . ($pb['zemin_tip'] ?? 'N/A'));
            $this->line("Pafta: " . ($pb['pafta_no'] ?? 'N/A'));

            if (isset($result['hesaplamalar'])) {
                $this->newLine();
                $this->info("Hesaplamalar:");
                $h = $result['hesaplamalar'];
                $this->line("TAKS: " . ($pb['taks'] ?? 'N/A') . '%');
                $this->line("KAKS: " . ($pb['kaks'] ?? 'N/A'));
                $this->line("Taban Alanı: " . ($h['taban_alani_formatted'] ?? 'N/A'));
                $this->line("İnşaat Alanı: " . ($h['insaat_alani_formatted'] ?? 'N/A'));
                $this->line("Maksimum Kat: " . ($h['maksimum_kat_sayisi'] ?? 'N/A'));
            }

            if (isset($result['oneriler']) && !empty($result['oneriler'])) {
                $this->newLine();
                $this->info("Öneriler:");
                foreach ($result['oneriler'] as $oneri) {
                    $this->line("• " . $oneri);
                }
            }

        } else {
            $this->error("❌ Hata: " . $result['message']);
        }

        return 0;
    }
}
