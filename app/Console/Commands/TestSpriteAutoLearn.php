<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TestSpriteAutoLearn extends Command
{
    protected $signature = 'testsprite:auto-learn';
    protected $description = 'Context7 kurallarını otomatik öğrenir ve TestSprite\'ı günceller';

    public function handle()
    {
        $this->info('🧠 TestSprite Context7 Auto-Learning başlıyor...');

        // 1. Context7 master dökümanlarını bul
        $masterDocs = $this->findMasterDocs();
        $this->info("📚 {$masterDocs->count()} master doküman bulundu");

        // 2. Kuralları parse et
        $rules = $this->parseRules($masterDocs);
        $this->info("✅ {$rules['forbidden']} yasaklı kural öğrenildi");
        $this->info("✅ {$rules['required']} zorunlu kural öğrenildi");

        // 3. Node.js rule loader'ı tetikle
        $this->info('🔄 Node.js rule loader güncelleniyor...');
        $this->triggerNodeRuleLoader();

        // 4. Laravel cache'e kaydet
        cache()->put('context7_rules', $rules, now()->addDay());

        $this->info('🎉 Öğrenme tamamlandı!');

        return 0;
    }

    private function findMasterDocs()
    {
        $paths = [
            'docs/ai-training/02-CONTEXT7-RULES-SIMPLIFIED.md',
            'docs/context7/rules/context7-rules.md',
            'README.md',
            'docs/README.md'
        ];

        return collect($paths)
            ->filter(fn($path) => File::exists(base_path($path)))
            ->map(fn($path) => base_path($path));
    }

    private function parseRules($docs)
    {
        $forbidden = [];
        $required = [];

        foreach ($docs as $docPath) {
            $content = File::get($docPath);

            // Yasaklı kurallar: ❌ YASAK "durum"
            preg_match_all('/[❌🚫]\s*(?:YASAK|FORBIDDEN).*?[\'"`]([^\'"` ]+)[\'"`]/i', $content, $matches);
            $forbidden = array_merge($forbidden, $matches[1] ?? []);

            // Zorunlu kurallar: ✅ DOĞRU "status"
            preg_match_all('/[✅✔️]\s*(?:DOĞRU|REQUIRED).*?[\'"`]([^\'"` ]+)[\'"`]/i', $content, $matches);
            $required = array_merge($required, $matches[1] ?? []);
        }

        return [
            'forbidden' => count(array_unique($forbidden)),
            'required' => count(array_unique($required)),
            'forbidden_list' => array_unique($forbidden),
            'required_list' => array_unique($required)
        ];
    }

    private function triggerNodeRuleLoader()
    {
        $scriptPath = base_path('testsprite/knowledge/context7-rule-loader.js');

        if (File::exists($scriptPath)) {
            exec("node -e \"const loader = require('{$scriptPath}'); loader.loadAllRules();\"");
        }
    }
}

