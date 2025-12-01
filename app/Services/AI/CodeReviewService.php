<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CodeReviewService
{
    private array $context7Rules = [
        'forbidden_patterns' => ['neo-', 'enabled', 'order'],
        'required_patterns' => ['Context7', 'Yalıhan'],
        'naming_conventions' => [
            'snake_case' => ['variables', 'functions'],
            'PascalCase' => ['classes'],
            'kebab-case' => ['css_classes'],
        ],
    ];

    public function reviewFile(string $filePath): array
    {
        if (! File::exists($filePath)) {
            return [];
        }

        $content = File::get($filePath);
        $lines = explode("\n", $content);
        $issues = [];

        // Run different types of analysis
        $issues = array_merge($issues, $this->analyzeContext7Compliance($lines, $filePath));
        $issues = array_merge($issues, $this->analyzeCodeQuality($lines, $filePath));
        $issues = array_merge($issues, $this->analyzeSecurity($lines, $filePath));
        $issues = array_merge($issues, $this->analyzePerformance($lines, $filePath));
        $issues = array_merge($issues, $this->analyzeMaintainability($lines, $filePath));

        return $issues;
    }

    private function analyzeContext7Compliance(array $lines, string $filePath): array
    {
        $issues = [];

        foreach ($lines as $lineNumber => $line) {
            $lineNum = $lineNumber + 1;

            // Check for forbidden patterns
            foreach ($this->context7Rules['forbidden_patterns'] as $pattern) {
                if (str_contains($line, $pattern)) {
                    $issues[] = [
                        'line' => $lineNum,
                        'type' => 'Context7 Violation',
                        'severity' => 'high',
                        'message' => "Forbidden pattern '{$pattern}' detected",
                        'suggestion' => $this->getContext7Suggestion($pattern),
                        'auto_fixable' => true,
                        'fix' => $this->getContext7Fix($pattern, $line),
                        'category' => 'compliance',
                    ];
                }
            }

            // Check CSS framework violations
            if (str_contains($line, 'btn-') || str_contains($line, 'card-')) {
                $issues[] = [
                    'line' => $lineNum,
                    'type' => 'CSS Framework Violation',
                    'severity' => 'medium',
                    'message' => 'Bootstrap classes detected. Use only Tailwind CSS.',
                    'suggestion' => 'Replace Bootstrap classes with Tailwind equivalents',
                    'auto_fixable' => false,
                    'fix' => null,
                    'category' => 'compliance',
                ];
            }

            // Check for missing transitions
            if (str_contains($line, 'hover:') && ! str_contains($line, 'transition')) {
                $issues[] = [
                    'line' => $lineNum,
                    'type' => 'Missing Transition',
                    'severity' => 'medium',
                    'message' => 'Interactive elements must have transitions',
                    'suggestion' => 'Add transition-all duration-200 ease-in-out',
                    'auto_fixable' => true,
                    'fix' => str_replace($line, $line.' transition-all duration-200 ease-in-out', $line),
                    'category' => 'compliance',
                ];
            }

            // Check for dark mode support
            if (str_contains($line, 'bg-white') && ! str_contains($line, 'dark:bg-')) {
                $issues[] = [
                    'line' => $lineNum,
                    'type' => 'Missing Dark Mode',
                    'severity' => 'medium',
                    'message' => 'Dark mode support required for all styling',
                    'suggestion' => 'Add dark mode variant (e.g., dark:bg-gray-800)',
                    'auto_fixable' => true,
                    'fix' => str_replace('bg-white', 'bg-white dark:bg-gray-800', $line),
                    'category' => 'compliance',
                ];
            }
        }

        return $issues;
    }

    private function analyzeCodeQuality(array $lines, string $filePath): array
    {
        $issues = [];

        foreach ($lines as $lineNumber => $line) {
            $lineNum = $lineNumber + 1;

            // Long line detection
            if (strlen($line) > 120) {
                $issues[] = [
                    'line' => $lineNum,
                    'type' => 'Code Style',
                    'severity' => 'low',
                    'message' => 'Line too long ('.strlen($line).' characters)',
                    'suggestion' => 'Break long lines for better readability',
                    'auto_fixable' => false,
                    'fix' => null,
                    'category' => 'quality',
                ];
            }

            // TODO/FIXME detection
            if (preg_match('/\b(TODO|FIXME|HACK)\b/i', $line)) {
                $issues[] = [
                    'line' => $lineNum,
                    'type' => 'Technical Debt',
                    'severity' => 'medium',
                    'message' => 'TODO/FIXME comment found',
                    'suggestion' => 'Create a proper task or issue for this item',
                    'auto_fixable' => false,
                    'fix' => null,
                    'category' => 'quality',
                ];
            }

            // Unused variables (basic detection)
            if (preg_match('/\$(\w+)\s*=/', $line, $matches) && ! str_contains(implode("\n", $lines), '$'.$matches[1])) {
                $issues[] = [
                    'line' => $lineNum,
                    'type' => 'Unused Variable',
                    'severity' => 'low',
                    'message' => "Variable \${$matches[1]} appears to be unused",
                    'suggestion' => 'Remove unused variable or use it',
                    'auto_fixable' => false,
                    'fix' => null,
                    'category' => 'quality',
                ];
            }

            // Missing null checks
            if (str_contains($line, '->') && ! str_contains($line, '??') && ! str_contains($line, '?->')) {
                if (str_contains($line, '$') && ! str_contains($line, 'isset(')) {
                    $issues[] = [
                        'line' => $lineNum,
                        'type' => 'Potential Null Reference',
                        'severity' => 'medium',
                        'message' => 'Possible null reference without null check',
                        'suggestion' => 'Use null coalescing operator (??) or null-safe operator (?.)',
                        'auto_fixable' => false,
                        'fix' => null,
                        'category' => 'quality',
                    ];
                }
            }
        }

        return $issues;
    }

    private function analyzeSecurity(array $lines, string $filePath): array
    {
        $issues = [];

        foreach ($lines as $lineNumber => $line) {
            $lineNum = $lineNumber + 1;

            // SQL injection risks
            if (str_contains($line, 'DB::raw') || str_contains($line, 'whereRaw')) {
                $issues[] = [
                    'line' => $lineNum,
                    'type' => 'Security Risk',
                    'severity' => 'high',
                    'message' => 'Raw SQL queries detected - potential SQL injection risk',
                    'suggestion' => 'Use parameter binding or Eloquent methods',
                    'auto_fixable' => false,
                    'fix' => null,
                    'category' => 'security',
                ];
            }

            // XSS risks
            if (str_contains($line, '{!!') && ! str_contains($line, 'csrf_token')) {
                $issues[] = [
                    'line' => $lineNum,
                    'type' => 'XSS Risk',
                    'severity' => 'critical',
                    'message' => 'Unescaped output detected - XSS vulnerability',
                    'suggestion' => 'Use {{ }} instead of {!! !!} for user data',
                    'auto_fixable' => true,
                    'fix' => str_replace('{!!', '{{', str_replace('!!}', '}}', $line)),
                    'category' => 'security',
                ];
            }

            // Hardcoded secrets
            if (preg_match('/(?:password|secret|key|token)\s*=\s*["\']([^"\']+)["\']/i', $line, $matches)) {
                if (strlen($matches[1]) > 8 && ! str_starts_with($matches[1], 'your_')) {
                    $issues[] = [
                        'line' => $lineNum,
                        'type' => 'Hardcoded Secret',
                        'severity' => 'critical',
                        'message' => 'Hardcoded secret detected',
                        'suggestion' => 'Move secrets to environment variables',
                        'auto_fixable' => false,
                        'fix' => null,
                        'category' => 'security',
                    ];
                }
            }

            // Missing CSRF protection
            if (str_contains($line, '<form') && ! str_contains($line, '@csrf')) {
                $issues[] = [
                    'line' => $lineNum,
                    'type' => 'Missing CSRF',
                    'severity' => 'high',
                    'message' => 'Form without CSRF protection',
                    'suggestion' => 'Add @csrf directive to form',
                    'auto_fixable' => true,
                    'fix' => str_replace('<form', '<form @csrf', $line),
                    'category' => 'security',
                ];
            }
        }

        return $issues;
    }

    private function analyzePerformance(array $lines, string $filePath): array
    {
        $issues = [];

        foreach ($lines as $lineNumber => $line) {
            $lineNum = $lineNumber + 1;

            // N+1 query detection
            if (str_contains($line, 'foreach') && (str_contains($line, '->') || str_contains($line, '::'))) {
                if (preg_match('/\$\w+\s*as\s*\$\w+/', $line)) {
                    $issues[] = [
                        'line' => $lineNum,
                        'type' => 'Potential N+1 Query',
                        'severity' => 'high',
                        'message' => 'Possible N+1 query in loop',
                        'suggestion' => 'Use eager loading with with() method',
                        'auto_fixable' => false,
                        'fix' => null,
                        'category' => 'performance',
                    ];
                }
            }

            // Missing eager loading
            if (str_contains($line, '::all()') || str_contains($line, '::get()')) {
                $issues[] = [
                    'line' => $lineNum,
                    'type' => 'Missing Eager Loading',
                    'severity' => 'medium',
                    'message' => 'Consider eager loading relationships',
                    'suggestion' => 'Use with() method to load relationships',
                    'auto_fixable' => false,
                    'fix' => null,
                    'category' => 'performance',
                ];
            }

            // Large array operations
            if (str_contains($line, 'array_map') || str_contains($line, 'array_filter')) {
                $issues[] = [
                    'line' => $lineNum,
                    'type' => 'Array Performance',
                    'severity' => 'low',
                    'message' => 'Large array operations may impact performance',
                    'suggestion' => 'Consider using collections or database queries',
                    'auto_fixable' => false,
                    'fix' => null,
                    'category' => 'performance',
                ];
            }

            // Missing caching
            if (str_contains($line, 'expensive') || str_contains($line, 'complex')) {
                $issues[] = [
                    'line' => $lineNum,
                    'type' => 'Caching Opportunity',
                    'severity' => 'low',
                    'message' => 'Complex operation could benefit from caching',
                    'suggestion' => 'Consider implementing caching for expensive operations',
                    'auto_fixable' => false,
                    'fix' => null,
                    'category' => 'performance',
                ];
            }
        }

        return $issues;
    }

    private function analyzeMaintainability(array $lines, string $filePath): array
    {
        $issues = [];

        // Function/method length analysis
        $functionLines = 0;
        $inFunction = false;

        foreach ($lines as $lineNumber => $line) {
            $lineNum = $lineNumber + 1;

            // Start of function
            if (preg_match('/^\s*(public|private|protected)?\s*function\s+\w+/', $line)) {
                $inFunction = true;
                $functionLines = 1;
                $functionStartLine = $lineNum;
            }

            if ($inFunction) {
                $functionLines++;

                // End of function
                if (trim($line) === '}' || preg_match('/^\s*}\s*$/', $line)) {
                    if ($functionLines > 50) {
                        $issues[] = [
                            'line' => $functionStartLine,
                            'type' => 'Large Function',
                            'severity' => 'medium',
                            'message' => "Function is too long ({$functionLines} lines)",
                            'suggestion' => 'Break down into smaller functions',
                            'auto_fixable' => false,
                            'fix' => null,
                            'category' => 'maintainability',
                        ];
                    }
                    $inFunction = false;
                    $functionLines = 0;
                }
            }

            // Deep nesting
            $indentLevel = (strlen($line) - strlen(ltrim($line))) / 4;
            if ($indentLevel > 5) {
                $issues[] = [
                    'line' => $lineNum,
                    'type' => 'Deep Nesting',
                    'severity' => 'medium',
                    'message' => 'Code is deeply nested (level '.$indentLevel.')',
                    'suggestion' => 'Refactor to reduce nesting levels',
                    'auto_fixable' => false,
                    'fix' => null,
                    'category' => 'maintainability',
                ];
            }

            // Magic numbers
            if (preg_match('/\b\d{2,}\b/', $line, $matches) && ! str_contains($line, 'line')) {
                if (! in_array($matches[0], ['10', '20', '50', '100', '200', '404', '500'])) {
                    $issues[] = [
                        'line' => $lineNum,
                        'type' => 'Magic Number',
                        'severity' => 'low',
                        'message' => "Magic number detected: {$matches[0]}",
                        'suggestion' => 'Replace with named constant',
                        'auto_fixable' => false,
                        'fix' => null,
                        'category' => 'maintainability',
                    ];
                }
            }
        }

        return $issues;
    }

    private function getContext7Suggestion(string $pattern): string
    {
        return match ($pattern) {
            'neo-' => 'Use Tailwind CSS classes instead of Neo Design System',
            'enabled' => 'Forbidden! Use "status" (boolean) instead (Context7 standard)',
            'order' => 'Forbidden! Use "display_order" instead (Context7 standard)',
            default => 'Follow Context7 naming conventions'
        };
    }

    private function getContext7Fix(string $pattern, string $line): ?string
    {
        return match ($pattern) {
            'neo-' => str_replace('neo-', '', $line),
            'enabled' => str_replace('enabled', 'status', $line),
            'order' => str_replace('order', 'display_order', $line),
            default => null
        };
    }

    public function applyFix(string $filePath, array $issue): bool
    {
        if (! $issue['auto_fixable'] || empty($issue['fix'])) {
            return false;
        }

        try {
            $content = File::get($filePath);
            $lines = explode("\n", $content);

            if (isset($lines[$issue['line'] - 1])) {
                $lines[$issue['line'] - 1] = $issue['fix'];
                File::put($filePath, implode("\n", $lines));

                return true;
            }
        } catch (\Exception $e) {
            Log::error('Failed to apply fix: '.$e->getMessage());
        }

        return false;
    }

    /**
     * Generate AI-powered suggestions using Context7 rules
     */
    public function generateAISuggestions(string $code, string $context = ''): array
    {
        // This would integrate with actual AI services (OpenAI, etc.)
        // For now, return rule-based suggestions

        $suggestions = [];

        // Performance suggestions
        if (str_contains($code, 'DB::') && ! str_contains($code, 'with(')) {
            $suggestions[] = [
                'type' => 'performance',
                'priority' => 'high',
                'message' => 'Consider using eager loading to prevent N+1 queries',
                'example' => 'Model::with([\'relation\'])->get() instead of Model::all()',
            ];
        }

        // Security suggestions
        if (str_contains($code, '$_GET') || str_contains($code, '$_POST')) {
            $suggestions[] = [
                'type' => 'security',
                'priority' => 'critical',
                'message' => 'Use Laravel request validation instead of direct superglobal access',
                'example' => '$request->validated() instead of $_POST',
            ];
        }

        return $suggestions;
    }
}
