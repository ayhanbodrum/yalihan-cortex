#!/usr/bin/env php
<?php
/**
 * Dropdown Auto-Fixer - Yalıhan Bekçi
 * 
 * Purpose: Automatically fix ALL dropdown readability issues
 * Date: 2025-11-01
 * Version: 1.0.0
 * 
 * Fixes Applied:
 * 1. dark:bg-gray-800 → dark:bg-gray-900
 * 2. dark:text-gray-100 → dark:text-white (in select tags)
 * 3. Add style="color-scheme: light dark;" to all select tags
 * 4. neo-select/neo-input → Tailwind classes (in select tags)
 * 5. Add transition classes to select tags without them
 */

class DropdownFixer
{
    private $basePath;
    private $dryRun = false;
    private $fixedFiles = [];
    private $totalFixes = 0;
    private $backupDir;

    public function __construct($basePath = null, $dryRun = false)
    {
        $this->basePath = $basePath ?? dirname(__DIR__, 2);
        $this->dryRun = $dryRun;
        $this->backupDir = $this->basePath . '/yalihan-bekci/backups/dropdown-fix-' . date('Y-m-d-His');
    }

    public function fix($path = 'resources/views/admin')
    {
        echo "🔧 Dropdown Auto-Fixer - Yalıhan Bekçi\n";
        echo "==========================================\n\n";

        if ($this->dryRun) {
            echo "⚠️  DRY RUN MODE - No files will be modified\n\n";
        } else {
            echo "📦 Creating backup: {$this->backupDir}\n";
            mkdir($this->backupDir, 0755, true);
            echo "✅ Backup directory created\n\n";
        }

        $fullPath = $this->basePath . '/' . $path;
        $files = $this->getBladeFiles($fullPath);

        echo "📁 Scanning: $path\n";
        echo "📄 Found " . count($files) . " Blade files\n\n";

        foreach ($files as $file) {
            $this->fixFile($file);
        }

        $this->generateReport();
    }

    private function getBladeFiles($directory)
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    private function fixFile($filePath)
    {
        $content = file_get_contents($filePath);
        $originalContent = $content;
        $relativePath = str_replace($this->basePath . '/', '', $filePath);
        $fixCount = 0;

        // Find all select tags
        preg_match_all('/<select[^>]*>/is', $content, $matches, PREG_OFFSET_CAPTURE);
        
        if (empty($matches[0])) {
            return; // No select tags in this file
        }

        // Create backup
        if (!$this->dryRun) {
            $backupPath = $this->backupDir . '/' . str_replace('/', '_', $relativePath);
            file_put_contents($backupPath, $originalContent);
        }

        // Process each select tag
        foreach ($matches[0] as $match) {
            $selectTag = $match[0];
            $originalTag = $selectTag;
            
            // Fix 1: dark:bg-gray-800 → dark:bg-gray-900
            if (strpos($selectTag, 'dark:bg-gray-800') !== false) {
                $selectTag = str_replace('dark:bg-gray-800', 'dark:bg-gray-900', $selectTag);
                $fixCount++;
            }

            // Fix 2: dark:text-gray-100 → dark:text-white
            if (strpos($selectTag, 'dark:text-gray-100') !== false) {
                $selectTag = str_replace('dark:text-gray-100', 'dark:text-white', $selectTag);
                $fixCount++;
            }

            // Fix 3: Add color-scheme if missing
            if (strpos($selectTag, 'color-scheme') === false && strpos($selectTag, 'style=') === false) {
                // No style attribute at all
                $selectTag = str_replace('<select', '<select style="color-scheme: light dark;"', $selectTag);
                $fixCount++;
            } elseif (strpos($selectTag, 'color-scheme') === false && strpos($selectTag, 'style=') !== false) {
                // Has style attribute but no color-scheme
                $selectTag = preg_replace(
                    '/style="([^"]*)"/',
                    'style="$1 color-scheme: light dark;"',
                    $selectTag
                );
                $fixCount++;
            }

            // Fix 4: Replace Neo Design classes
            if (strpos($selectTag, 'neo-select') !== false || strpos($selectTag, 'neo-input') !== false) {
                // Replace neo-select with Tailwind equivalent
                $selectTag = str_replace('neo-select', 'w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500', $selectTag);
                $selectTag = str_replace('neo-input', 'w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500', $selectTag);
                $fixCount++;
            }

            // Fix 5: Add transition if missing
            if (strpos($selectTag, 'transition-') === false) {
                // Find the class attribute and add transition
                if (preg_match('/class="([^"]*)"/', $selectTag, $classMatch)) {
                    $newClass = $classMatch[1] . ' transition-all duration-200';
                    $selectTag = str_replace('class="' . $classMatch[1] . '"', 'class="' . $newClass . '"', $selectTag);
                    $fixCount++;
                } else {
                    // No class attribute, add one
                    $selectTag = str_replace('<select', '<select class="transition-all duration-200"', $selectTag);
                    $fixCount++;
                }
            }

            // Replace in content
            if ($originalTag !== $selectTag) {
                $content = str_replace($originalTag, $selectTag, $content);
            }
        }

        if ($fixCount > 0) {
            if (!$this->dryRun) {
                file_put_contents($filePath, $content);
                echo "✅ Fixed: $relativePath ($fixCount fixes)\n";
            } else {
                echo "🔍 Would fix: $relativePath ($fixCount fixes)\n";
            }

            $this->fixedFiles[] = [
                'file' => $relativePath,
                'fixes' => $fixCount
            ];
            $this->totalFixes += $fixCount;
        }
    }

    private function generateReport()
    {
        echo "\n==========================================\n";
        echo "📊 FIX SUMMARY\n";
        echo "==========================================\n\n";

        echo "📄 Files Processed: " . count($this->fixedFiles) . "\n";
        echo "🔧 Total Fixes Applied: {$this->totalFixes}\n\n";

        if (empty($this->fixedFiles)) {
            echo "✅ All files are already Context7 compliant!\n";
            return;
        }

        echo "📝 FIXED FILES:\n";
        echo "==========================================\n\n";

        foreach ($this->fixedFiles as $file) {
            echo "✅ {$file['file']} ({$file['fixes']} fixes)\n";
        }

        if (!$this->dryRun) {
            echo "\n💾 Backup Location: {$this->backupDir}\n";
            echo "🔄 To restore: cp {$this->backupDir}/* back to original locations\n\n";
        }

        // Save report
        $this->saveReport();
    }

    private function saveReport()
    {
        $reportPath = $this->basePath . '/yalihan-bekci/reports/dropdown-fix-' . date('Y-m-d-His') . '.json';
        
        $report = [
            'fix_date' => date('Y-m-d H:i:s'),
            'dry_run' => $this->dryRun,
            'files_fixed' => count($this->fixedFiles),
            'total_fixes' => $this->totalFixes,
            'backup_location' => $this->backupDir,
            'fixed_files' => $this->fixedFiles,
            'fixes_applied' => [
                'dark_bg_gray_800_to_900' => 'Applied',
                'dark_text_gray_100_to_white' => 'Applied',
                'color_scheme_property_added' => 'Applied',
                'neo_classes_replaced' => 'Applied',
                'transition_classes_added' => 'Applied'
            ]
        ];

        file_put_contents($reportPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        echo "💾 Report saved: " . str_replace($this->basePath . '/', '', $reportPath) . "\n";
    }
}

// Parse command line arguments
$dryRun = in_array('--dry-run', $argv) || in_array('-d', $argv);

// Run the fixer
$fixer = new DropdownFixer(null, $dryRun);
$fixer->fix();

echo "\n✅ DONE!\n";

