<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$authorityPath = $root.DIRECTORY_SEPARATOR.'.context7'.DIRECTORY_SEPARATOR.'authority.json';
if (! file_exists($authorityPath)) {
    fwrite(STDERR, json_encode(['error' => 'authority.json not found', 'path' => $authorityPath], JSON_UNESCAPED_UNICODE).PHP_EOL);
    exit(2);
}
$json = file_get_contents($authorityPath);
if ($json === false) {
    fwrite(STDERR, json_encode(['error' => 'authority.json read failed', 'path' => $authorityPath], JSON_UNESCAPED_UNICODE).PHP_EOL);
    exit(2);
}
$data = json_decode($json, true);
if (! is_array($data)) {
    fwrite(STDERR, json_encode(['error' => 'authority.json invalid json'], JSON_UNESCAPED_UNICODE).PHP_EOL);
    exit(2);
}

function collectStrings($node, array &$out): void
{
    if (is_string($node)) {
        $s = trim($node);
        if ($s !== '') {
            $out[$s] = true;
        }

        return;
    }
    if (is_array($node)) {
        foreach ($node as $k => $v) {
            collectStrings($v, $out);
        }

        return;
    }
    if (is_object($node)) {
        foreach (get_object_vars($node) as $v) {
            collectStrings($v, $out);
        }
    }
}

$wordTokens = [
    'enabled',
    'aktif',
    'durum',
    'is_active',
    'order',
    'musteri',
    'sehir_id',
    'semt_id',
];
$prefixTokens = [
    'neo-',
    'btn-',
    'crm.',
];

$scanDirs = [
    $root.'/app',
    $root.'/routes',
    $root.'/resources',
    $root.'/public',
    $root.'/Modules',
];

$extensions = ['php', 'js', 'ts', 'vue', 'blade.php', 'css', 'scss', 'html'];

function shouldScan(string $path, array $extensions): bool
{
    $p = strtolower($path);
    foreach ($extensions as $ext) {
        if (str_ends_with($p, '.'.$ext) || ($ext === 'blade.php' && str_ends_with($p, 'blade.php'))) {
            return true;
        }
    }

    return false;
}

function fileExt(string $path): string
{
    $p = strtolower($path);
    if (str_ends_with($p, 'blade.php')) {
        return 'blade.php';
    }
    $pos = strrpos($p, '.');

    return $pos === false ? '' : substr($p, $pos + 1);
}

$violations = [];
foreach ($scanDirs as $dir) {
    if (! is_dir($dir)) {
        continue;
    }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        $path = (string) $file;
        if (str_contains($path, DIRECTORY_SEPARATOR.'build'.DIRECTORY_SEPARATOR)) {
            continue;
        }
        if (str_contains($path, DIRECTORY_SEPARATOR.'node_modules'.DIRECTORY_SEPARATOR)) {
            continue;
        }
        if (str_contains($path, DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR)) {
            continue;
        }
        if (! shouldScan($path, $extensions)) {
            continue;
        }
        $content = file_get_contents($path);
        if ($content === false || $content === '') {
            continue;
        }
        $lines = preg_split('/\R/', $content);
        if (! $lines) {
            continue;
        }
        $ext = fileExt($path);
        foreach ($lines as $i => $line) {
            $lower = strtolower($line);
            foreach ($wordTokens as $tok) {
                $re = '/(^|[^a-zA-Z_])'.preg_quote($tok, '/').'([^a-zA-Z_]|$)/i';
                if (preg_match($re, $line)) {
                    if (strtolower($tok) === 'order') {
                        if ($ext !== 'php' && $ext !== 'blade.php') {
                            continue;
                        }
                        if (preg_match('/border/i', $line)) {
                            continue;
                        }
                    }
                    if (strtolower($tok) === 'enabled') {
                        if ($ext !== 'php' && $ext !== 'blade.php') {
                            continue;
                        }
                    }
                    if (strtolower($tok) === 'durum' || strtolower($tok) === 'aktif') {
                        if ($ext !== 'php' && $ext !== 'blade.php') {
                            continue;
                        }
                    }
                    $violations[] = [
                        'file' => $path,
                        'line' => $i + 1,
                        'pattern' => $tok,
                        'snippet' => mb_substr(trim($line), 0, 200),
                    ];
                }
            }
            foreach ($prefixTokens as $tok) {
                $needle = strtolower($tok);
                if ($needle !== '' && str_contains($lower, $needle)) {
                    $violations[] = [
                        'file' => $path,
                        'line' => $i + 1,
                        'pattern' => $tok,
                        'snippet' => mb_substr(trim($line), 0, 200),
                    ];
                }
            }
            if (preg_match('/route::prefix\([^\)]*\)\s*->prefix\(/i', $line)) {
                $violations[] = [
                    'file' => $path,
                    'line' => $i + 1,
                    'pattern' => 'double prefix',
                    'snippet' => mb_substr(trim($line), 0, 200),
                ];
            }
        }
    }
}

$result = [
    'authority' => $authorityPath,
    'rules_loaded' => count($wordTokens) + count($prefixTokens),
    'violations' => $violations,
    'violations_count' => count($violations),
];

echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).PHP_EOL;

if (count($violations) > 0) {
    exit(1);
}
exit(0);
