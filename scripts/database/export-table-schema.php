<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

$table = 'ilanlar';
$db = config('database.connections.mysql.database');
$columns = DB::select('SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_KEY, EXTRA, COLUMN_COMMENT FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION', [$db, $table]);
$indexes = DB::select('SELECT INDEX_NAME, GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) AS COLUMNS, NON_UNIQUE FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? GROUP BY INDEX_NAME, NON_UNIQUE ORDER BY INDEX_NAME', [$db, $table]);
$create = DB::select("SHOW CREATE TABLE `$table`");
$ddl = isset($create[0]) ? (array) $create[0] : [];
$ddlText = '';
foreach ($ddl as $k => $v) {
    if (stripos($k, 'Create Table') !== false) {
        $ddlText = $v;
        break;
    }
}
$md = "# Tablo Şeması: $table\n\n";
$md .= "Veritabanı: `$db`\n\n";
$md .= "## Kolonlar\n\n";
$md .= "| Kolon | Tip | Null | Varsayılan | Key | Extra | Açıklama |\n|---|---|---|---|---|---|---|\n";
foreach ($columns as $c) {
    $md .= '| '.($c->COLUMN_NAME).' | '.($c->COLUMN_TYPE).' | '.($c->IS_NULLABLE).' | '.(is_null($c->COLUMN_DEFAULT) ? 'NULL' : str_replace("\n", ' ', (string) $c->COLUMN_DEFAULT)).' | '.($c->COLUMN_KEY).' | '.($c->EXTRA).' | '.str_replace('|', '\|', (string) $c->COLUMN_COMMENT)." |\n";
}
$md .= "\n## Indexler\n\n";
$md .= "| Index | Kolonlar | Unique |\n|---|---|---|\n";
foreach ($indexes as $i) {
    $md .= '| '.($i->INDEX_NAME).' | '.($i->COLUMNS).' | '.((int) $i->NON_UNIQUE === 0 ? 'Yes' : 'No')." |\n";
}
if ($ddlText) {
    $md .= "\n## DDL\n\n";
    $md .= "```sql\n".$ddlText."\n```\n";
}
$file = __DIR__.'/../docs/ilanlar-schema.md';
file_put_contents($file, $md);
echo "WROTE: $file\n";
