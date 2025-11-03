<?php
// scripts/context7-forbidden-auto-fix.php
// Forbidden alan adlarını migration dışındaki dosyalarda otomatik düzeltir ve raporlar

$root = __DIR__ . '/..';
$replacements = [
    'category_id' => 'category_id',
    'status' => 'status',
    'il_id' => 'il_id',
    'il' => 'il',
    'region_id' => 'region_id', // veya kaldırılacaksa 'REMOVE' olarak işaretlenebilir
];

// Kod-level forbidden pattern'lar (değişken, array key, migration field, model property)
$codePatterns = [
    // değişken
    function($forbidden, $replacement) {
        return [
            '/\\$[a-zA-Z_]*\\b'.preg_quote($forbidden,'/').'\\b/' => '$'.str_replace($forbidden, $replacement, '$0'),
        ];
    },
    // array key
    function($forbidden, $replacement) {
        return [
            '/[\'\"]'.preg_quote($forbidden,'/').'[\'\"][\s]*=>/' => "'{$replacement}' =>",
        ];
    },
    // migration field
    function($forbidden, $replacement) {
        return [
            '/\\$table->(string|integer|boolean|enum|tinyInteger|smallInteger|bigInteger|date|datetime|text|json)\\([\'\"]'.preg_quote($forbidden,'/').'[\'\"]/' => "\$table->$1('{$replacement}'",
        ];
    },
    // model property
    function($forbidden, $replacement) {
        return [
            '/public\\s+\\$?'.preg_quote($forbidden,'/').'\\b/' => 'public $'.$replacement,
        ];
    },
];
$ignoreDirs = ['vendor','node_modules','storage','public/build','database/migrations'];
$results = [];
$fixed = 0;
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
foreach ($rii as $file) {
    if (!$file->isFile()) continue;
    $path = $file->getPathname();
    $rel = substr($path, strlen($root)+1);
    $skip = false;
    foreach ($ignoreDirs as $d) { if (strpos($rel, $d . '/') === 0) { $skip = true; break; } }
    if ($skip) continue;
    $ext = pathinfo($rel, PATHINFO_EXTENSION);
    if (!in_array($ext, ['php','js','css','json','md','sql']) && substr($rel, -10) !== '.blade.php') continue;
    $content = file_get_contents($path);
    $original = $content;
    foreach ($replacements as $forbidden => $replacement) {
        if ($replacement === 'REMOVE') continue;
        // Sadece kod-level forbidden pattern'ları uygula
        foreach ($codePatterns as $patternFunc) {
            foreach ($patternFunc($forbidden, $replacement) as $rx => $rep) {
                $content = preg_replace($rx, $rep, $content);
            }
        }
    }
    if ($content !== $original) {
        file_put_contents($path, $content);
        $results[] = $rel;
        $fixed++;
        echo "[DÜZELTİLDİ] $rel\n";
    }
}
echo "\nToplam dosya düzeltildi: $fixed\n";
if ($fixed === 0) {
    echo "(Sadece metinsel/etiketsel kullanımlar bulunduysa, bunlar Context7 uyumunu bozmaz.)\n";
}
