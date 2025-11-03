<?php
// scripts/doktor.php
// Tüm Context7 migration, forbidden scan, compliance, auto-fix ve şema export işlemlerini tek noktadan yürüten "doktor" scripti.
// Kullanım: php scripts/doktor.php [temizle|fix|scan|export|check|full]


$usage = "Kullanım: php scripts/doktor.php [temizle|fix|scan|export|check|full]\n\n" .
    "temizle: Migration dosyalarını Context7/Laravel uyumlu hale getirir\n" .
    "fix: Kodda Context7 kural ihlallerini otomatik düzeltir\n" .
    "scan: Yasak/legacy alanları ve forbidden pattern'leri tarar (tüm varyasyonlar, blade/seed dahil)\n" .
    "export: Tüm önemli tabloların şemasını markdown olarak export eder\n" .
    "check: Context7 compliance ve önleyici kontrolleri çalıştırır\n" .
    "full: Tüm işlemleri sırasıyla uygular ve raporlar (derin forbidden scan, Türkçe-İngilizce karışıklık, tekrarlar)\n";

$cmd = $argv[1] ?? null;
if (!$cmd || in_array($cmd, ['-h', '--help', 'help'])) {
    echo $usage;
    exit(0);
}

function run($desc, $cmd) {
    echo "\n==== $desc ====" . PHP_EOL;
    passthru($cmd, $code);
    if ($code !== 0) {
        echo "[HATA] $desc başarısız!\n";
        exit($code);
    }
}

$root = realpath(__DIR__ . '/..');

switch ($cmd) {
    case 'temizle':
        run('Migration Temizleyici', 'php scripts/context7-migration-auto-cleaner.php');
        break;
    case 'fix':
        run('Otomatik Kod Düzeltici', 'bash scripts/context7-auto-fix.sh');
        break;
    case 'scan':
        run('Yasak Alan/Pattern Taraması', 'php scripts/context7-forbidden-scan.php');
        break;
    case 'export':
        run('Tablo Şeması Export (ilanlar)', 'php scripts/export-table-schema.php');
        run('Tablo Şeması Export (çoklu)', 'php scripts/export-multi-table-schema.php');
        break;
    case 'check':
        run('Context7 Önleyici Kontrol', 'bash scripts/context7-prevent-violations.sh');
        break;
    case 'full':
        // 1. Migration syntax fixer (otomatik parantez düzeltici)
        echo "\n==== Migration Syntax Otomatik Onarıcı ====".PHP_EOL;
        passthru('php scripts/context7-migration-syntax-fixer.php', $syntaxCode);
        if ($syntaxCode !== 0) {
            echo "[HATA] Migration syntax otomatik düzeltici başarısız!\n";
            exit($syntaxCode);
        }
        run('Migration Temizleyici', 'php scripts/context7-migration-auto-cleaner.php');
        run('Otomatik Kod Düzeltici', 'bash scripts/context7-auto-fix.sh');
        run('Forbidden Auto-Fix', 'php scripts/context7-forbidden-auto-fix.php');

        // DERİN FORBIDDEN SCAN (tüm varyasyonlar, blade/seed dahil)
        echo "\n==== Yasaklı Alan/Pattern Derin Taraması (Tüm Varyasyonlar, Blade/Seed Dahil) ====".PHP_EOL;
        $forbiddenList = [
            'status','durum','is_active','aktif','site','sites','ayar','ayarlari','settings','setting',
            'il','city','il_id','city_id','ilce','district','ilce_id','district_id','mahalle','neighborhood','mahalle_id','neighborhood_id',
            'category_id','kategori_id','company_setting','company_settings','company_ayar','company_ayarlari'
        ];
        $forbiddenRx = implode('|', array_map('preg_quote', $forbiddenList));
        $scanTargets = [
            'app/Models','app/Http/Controllers','app/Modules','resources/views','database/seeders','database/migrations'
        ];
        $summary = [];
        $repeatMap = [];
        $trEnMix = [];
        foreach ($scanTargets as $dir) {
            $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/'.$dir, FilesystemIterator::SKIP_DOTS));
            foreach ($rii as $file) {
                if (!$file->isFile()) continue;
                $path = $file->getPathname();
                $rel = substr($path, strlen($root)+1);
                $ext = pathinfo($rel, PATHINFO_EXTENSION);
                if (!in_array($ext, ['php','js','json','md','sql']) && substr($rel, -10) !== '.blade.php') continue;
                $lines = @file($path);
                if (!$lines) continue;
                $found = [];
                foreach ($lines as $ln => $text) {
                    if (preg_match_all('/\b('.$forbiddenRx.')\b/i', $text, $mm)) {
                        foreach ($mm[1] as $match) {
                            $found[] = strtolower($match);
                            // Türkçe-İngilizce karışık değer/alan kontrolü
                            if (preg_match('/[a-zA-Z]+/', $match) && preg_match('/[ğüşıöçĞÜŞİÖÇ]+/', $match)) {
                                $trEnMix[$rel][] = [ 'line' => $ln+1, 'text' => trim($text), 'match' => $match ];
                            }
                        }
                    }
                }
                if (count($found) > 0) {
                    $summary[$rel] = array_count_values($found);
                    // Tekrarlayan forbidden alanlar
                    foreach (array_count_values($found) as $k=>$v) {
                        if ($v > 1) $repeatMap[$rel][$k] = $v;
                    }
                }
            }
        }
        // Raporlama
        echo "\n==== Yasaklı Alan Kullanım Özeti ====".PHP_EOL;
        foreach ($summary as $file=>$counts) {
            echo "$file: ";
            foreach ($counts as $k=>$v) echo "$k($v) ";
            echo "\n";
        }
        echo "\n==== Tekrarlayan Yasaklı Alanlar ====".PHP_EOL;
        foreach ($repeatMap as $file=>$fields) {
            echo "$file: ";
            foreach ($fields as $k=>$v) echo "$k($v) ";
            echo "\n";
        }
        echo "\n==== Türkçe-İngilizce Karışık Alan/Değerler ====".PHP_EOL;
        foreach ($trEnMix as $file=>$arr) {
            foreach ($arr as $item) {
                echo "$file:{$item['line']}: [{$item['match']}] ".mb_substr($item['text'],0,120)."\n";
            }
        }

        // DOMAIN ANALİZ & TEDAVİ MODÜLÜ
        echo "\n==== Kategori/Alt Kategori/Yayın Tipi/Özellik Domain Analiz & Tedavi Modülü ====".PHP_EOL;
        passthru('php scripts/context7-domain-analyzer.php', $domainCode);
        if ($domainCode !== 0) {
            echo "[UYARI] Domain analiz modülü hata ile tamamlandı!\n";
        }

        // YENİ MODÜLER ANALİZÖRLER
        echo "\n==== MIGRATION ANALYZER ====".PHP_EOL;
        passthru('php scripts/context7-migration-analyzer.php');
        echo "\n==== MODEL ANALYZER ====".PHP_EOL;
        passthru('php scripts/context7-model-analyzer.php');
        echo "\n==== CONTROLLER ANALYZER ====".PHP_EOL;
        passthru('php scripts/context7-controller-analyzer.php');
        echo "\n==== BLADE ANALYZER ====".PHP_EOL;
        passthru('php scripts/context7-blade-analyzer.php');
        echo "\n==== SEED ANALYZER ====".PHP_EOL;
        passthru('php scripts/context7-seed-analyzer.php');
        echo "\n==== TECH ANALYZER ====".PHP_EOL;
        passthru('php scripts/context7-tech-analyzer.php');

        run('Tablo Şeması Export (ilanlar)', 'php scripts/export-table-schema.php');
        run('Tablo Şeması Export (çoklu)', 'php scripts/export-multi-table-schema.php');
        run('Context7 Önleyici Kontrol', 'bash scripts/context7-prevent-violations.sh');
        break;
    default:
        echo $usage;
        exit(1);
}

echo "\n[DOKTOR] Tüm işlemler başarıyla tamamlandı!\n";
