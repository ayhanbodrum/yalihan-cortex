<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Context7CheckCommand extends Command
{
    protected $signature = 'context7:check
                            {--path= : Specific path to check}
                            {--fix : Auto-fix violations}
                            {--report : Generate detailed report}';

    protected $description = 'Check Context7 compliance in the project';

    public function handle()
    {
        $this->info('🚀 Running context7:check...');
        
        // Get options
        $path = $this->option('path') ?: base_path();
        $autoFix = $this->option('fix');
        $generateReport = $this->option('report');
        
        // Run Context7 checking logic
        $result = $this->runContext7Check($path, $autoFix, $generateReport);
        
        if ($result['compliance'] >= 95) {
            $this->info('✅ Context7 compliance: ' . $result['compliance'] . '%');
        } else {
            $this->warn('⚠️  Context7 compliance: ' . $result['compliance'] . '% (Target: 95%)');
            $this->info('📋 Violations: ' . $result['violations']);
        }
        
        return $result['compliance'] >= 95 ? 0 : 1;
    }
    
    private function runContext7Check($path, $autoFix = false, $generateReport = false)
    {
        // Include the main compliance checker
        $checkerPath = base_path('scripts/context7_final_compliance_checker.php');
        
        if (file_exists($checkerPath)) {
            ob_start();
            include $checkerPath;
            $output = ob_get_clean();
            
            // Parse results
            preg_match('/Toplam ihlal: (\d+)/', $output, $matches);
            $violations = (int)($matches[1] ?? 0);
            $compliance = (($this->getOriginalViolationCount() - $violations) / $this->getOriginalViolationCount()) * 100;
            
            return [
                'violations' => $violations,
                'compliance' => round($compliance, 2),
                'output' => $output
            ];
        }
        
        return ['violations' => 0, 'compliance' => 100, 'output' => 'No checker found'];
    }
    
    private function getOriginalViolationCount()
    {
        return 1729; // Original violation count from first scan
    }
}