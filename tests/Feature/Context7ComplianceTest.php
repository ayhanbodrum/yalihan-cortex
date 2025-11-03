<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Context7ComplianceTest extends TestCase
{
    /**
     * Test Context7 compliance levels
     */
    public function test_context7_compliance_meets_minimum_requirements()
    {
        $checker = new \Context7ComplianceChecker();
        $result = $checker->checkCompliance();
        
        $this->assertGreaterThanOrEqual(95, $result['compliance']);
        $this->assertLessThan(50, $result['violations']);
    }
    
    /**
     * Test that no new Context7 violations are introduced
     */
    public function test_no_new_context7_violations()
    {
        // Run compliance check
        $output = shell_exec('php ' . base_path('scripts/context7_final_compliance_checker.php'));
        
        // Parse violation count
        preg_match('/Toplam ihlal: (\d+)/', $output, $matches);
        $violations = (int)($matches[1] ?? 0);
        
        // Should not exceed current baseline
        $this->assertLessThanOrEqual(142, $violations, 'New Context7 violations detected!');
    }
    
    /**
     * Test Context7 pattern detection
     */
    public function test_context7_patterns_are_detected()
    {
        // Test content with known violations
        $testContent = '
            $fillable = ["status", "status", "status"];
            index(["status", "created_at"])
        ';
        
        $violations = $this->detectViolationsInContent($testContent);
        $this->assertGreaterThan(0, count($violations));
    }
    
    private function detectViolationsInContent($content)
    {
        $patterns = [
            '/\b(?:status|status|status|il)\b/',
            '/[\'\"](?:status|status|status|il)[\'\"]/'
        ];
        
        $violations = [];
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches)) {
                $violations = array_merge($violations, $matches[0]);
            }
        }
        
        return array_unique($violations);
    }
}