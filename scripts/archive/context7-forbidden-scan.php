<?php

$root = __DIR__.'/..';
$patterns = [
    // Sadece kod-level forbidden alanlar: değişken, array key, migration field, model property
    '/\$[a-zA-Z_]*\b(status|is_active|il_id|il|category_id)\b/', // değişken
    '/[\'\"](status|is_active|il_id|il|category_id)[\'\"][\s]*=>/', // array key
    '/\$table->(string|integer|boolean|enum|tinyInteger|smallInteger|bigInteger|date|datetime|text|json)\([\'\"](status|is_active|il_id|il|category_id)[\'\"]/', // migration field
    '/public\s+\$?(status|is_active|il_id|il|category_id)\b/', // model property
    // Metinsel/etiketsel forbidden: label, yorum, hata mesajı, açıklama, UI metni
    '/(^|[^a-z_])(status|is_active|il_id|il|category_id)([^a-z_]|$)/i', // genel (false-positive olabilir)
    '/region_id[\s\S]*kaldırıldı/i',
];
$ignoreDirs = ['vendor', 'node_modules', 'storage', 'public/build'];
$results = [];
$falsePositives = [];
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
foreach ($rii as $file) {
    if (! $file->isFile()) {
        continue;
    }
    $path = $file->getPathname();
    $rel = substr($path, strlen($root) + 1);
    $skip = false;
    foreach ($ignoreDirs as $d) {
        if (strpos($rel, $d.'/') === 0) {
            $skip = true;
            break;
        }
    }
    if ($skip) {
        continue;
    }
    $ext = pathinfo($rel, PATHINFO_EXTENSION);
    // .blade.php dosyalarını da yakala
    if (! in_array($ext, ['php', 'js', 'css', 'json', 'md', 'sql']) && substr($rel, -10) !== '.blade.php') {
        continue;
    }
    $lines = @file($path);
    if (! $lines) {
        continue;
    }
    foreach ($lines as $ln => $text) {
        $isReal = false;
        $isFalsePositive = false;
        // Kod-level forbidden pattern'lar
        foreach (array_slice($patterns, 0, 4) as $rx) {
            if (preg_match($rx, $text)) {
                $isReal = true;
                break;
            }
        }
        // Metinsel/etiket forbidden pattern'lar (sadece kod-level bulunmadıysa)
        if (! $isReal) {
            foreach (array_slice($patterns, 4) as $rx) {
                if (preg_match($rx, $text)) {
                    $isFalsePositive = true;
                    break;
                }
            }
        }
        if ($isReal) {
            $results[] = ['file' => $rel, 'line' => $ln + 1, 'text' => trim($text)];
        } elseif ($isFalsePositive) {
            $falsePositives[] = ['file' => $rel, 'line' => $ln + 1, 'text' => trim($text)];
        }
    }
}
usort($results, function ($a, $b) {
    return strcmp($a['file'], $b['file']) ?: ($a['line'] - $b['line']);
});

echo '==== GERÇEK KURAL İHLALLERİ (Kod-level) ===='.PHP_EOL;
if (count($results) === 0) {
    echo 'YOK'.PHP_EOL;
} else {
    foreach (array_slice($results, 0, 200) as $r) {
        echo $r['file'].':'.$r['line'].': '.$r['text'].PHP_EOL;
    }
}
echo 'TOTAL_REAL_MATCHES='.count($results).PHP_EOL;

echo PHP_EOL.'==== METİNSEL/ETİKETSEL (False-Positive) ===='.PHP_EOL;
if (count($falsePositives) === 0) {
    echo 'YOK'.PHP_EOL;
} else {
    foreach (array_slice($falsePositives, 0, 200) as $r) {
        echo $r['file'].':'.$r['line'].': '.$r['text'].PHP_EOL;
    }
}
echo 'TOTAL_FALSE_POSITIVES='.count($falsePositives).PHP_EOL;
