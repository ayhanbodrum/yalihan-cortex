#!/usr/bin/env php
<?php

/**
 * Bootstrap → Tailwind CSS Migration Script
 * Context7 Standardı: C7-TAILWIND-MIGRATION-2025-11-06
 * 
 * This script automatically converts Bootstrap classes to Tailwind CSS
 */

class BootstrapToTailwindMigrator
{
    private $conversions = [
        // Buttons
        'btn btn-primary' => 'px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 font-medium shadow-sm hover:shadow-md hover:scale-105 active:scale-95',
        'btn btn-success' => 'px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all duration-200 font-medium shadow-sm hover:shadow-md hover:scale-105 active:scale-95',
        'btn btn-danger' => 'px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-all duration-200 font-medium shadow-sm hover:shadow-md hover:scale-105 active:scale-95',
        'btn btn-warning' => 'px-4 py-2.5 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-all duration-200 font-medium shadow-sm hover:shadow-md hover:scale-105 active:scale-95',
        'btn btn-info' => 'px-4 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg transition-all duration-200 font-medium shadow-sm hover:shadow-md hover:scale-105 active:scale-95',
        'btn btn-secondary' => 'px-4 py-2.5 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all duration-200 font-medium shadow-sm hover:shadow-md hover:scale-105 active:scale-95',
        'btn btn-light' => 'px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 rounded-lg transition-all duration-200 font-medium shadow-sm hover:shadow-md hover:scale-105 active:scale-95',
        'btn btn-dark' => 'px-4 py-2.5 bg-gray-900 hover:bg-gray-800 text-white rounded-lg transition-all duration-200 font-medium shadow-sm hover:shadow-md hover:scale-105 active:scale-95',
        'btn-sm' => 'px-3 py-2 text-sm',
        'btn-lg' => 'px-6 py-3 text-lg',
        
        // Forms
        'form-control' => 'w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200',
        'form-select' => 'w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 transition-all duration-200',
        'form-label' => 'block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2',
        'form-check-input' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500',
        'form-check-label' => 'ml-2 text-sm font-medium text-gray-700 dark:text-gray-300',
        
        // Cards
        'card' => 'bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm',
        'card-header' => 'px-6 py-4 border-b border-gray-200 dark:border-gray-700',
        'card-body' => 'p-6',
        'card-footer' => 'px-6 py-4 border-t border-gray-200 dark:border-gray-700',
        
        // Layout
        'container' => 'container mx-auto px-4',
        'container-fluid' => 'w-full px-4',
        'row' => 'grid grid-cols-12 gap-4',
        'col-md-6' => 'col-span-12 md:col-span-6',
        'col-md-4' => 'col-span-12 md:col-span-4',
        'col-md-3' => 'col-span-12 md:col-span-3',
        
        // Utilities
        'text-center' => 'text-center',
        'text-right' => 'text-right',
        'mt-3' => 'mt-3',
        'mb-3' => 'mb-3',
        'd-flex' => 'flex',
        'justify-content-between' => 'justify-between',
        'align-items-center' => 'items-center',
    ];
    
    private $processed = 0;
    private $errors = 0;
    private $backupDir = '';
    
    public function __construct()
    {
        $this->backupDir = base_path('backups/bootstrap-migration-' . date('Y-m-d-His'));
    }
    
    public function migrate(string $targetDir): void
    {
        echo "🚀 Bootstrap → Tailwind CSS Migration\n";
        echo "═══════════════════════════════════════\n\n";
        
        // Create backup directory
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
            echo "📁 Backup directory created: {$this->backupDir}\n\n";
        }
        
        // Find all blade files
        $files = $this->findBladeFiles($targetDir);
        echo "📝 Found " . count($files) . " blade files to check\n\n";
        
        foreach ($files as $file) {
            $this->processFile($file);
        }
        
        echo "\n═══════════════════════════════════════\n";
        echo "✅ Migration Complete!\n";
        echo "📊 Processed: {$this->processed} files\n";
        echo "❌ Errors: {$this->errors}\n";
        echo "📁 Backups: {$this->backupDir}\n";
    }
    
    private function findBladeFiles(string $dir): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $filename = $file->getFilename();
                if (str_ends_with($filename, '.blade.php')) {
                    $files[] = $file->getPathname();
                }
            }
        }
        
        return $files;
    }
    
    private function processFile(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $originalContent = $content;
        
        // Check if file has Bootstrap classes
        $hasBootstrap = false;
        foreach (array_keys($this->conversions) as $bootstrap) {
            if (strpos($content, $bootstrap) !== false) {
                $hasBootstrap = true;
                break;
            }
        }
        
        if (!$hasBootstrap) {
            return; // Skip files without Bootstrap
        }
        
        // Backup original file
        $relativePath = str_replace(base_path() . '/', '', $filePath);
        $backupPath = $this->backupDir . '/' . str_replace('/', '_', $relativePath);
        file_put_contents($backupPath, $originalContent);
        
        // Convert Bootstrap to Tailwind
        foreach ($this->conversions as $bootstrap => $tailwind) {
            $content = str_replace($bootstrap, $tailwind, $content);
        }
        
        // Save converted file
        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
            $this->processed++;
            echo "✅ Converted: {$relativePath}\n";
        }
    }
}

// Run migration
if (php_sapi_name() === 'cli') {
    $targetDir = $argv[1] ?? base_path('resources/views/admin');
    
    $migrator = new BootstrapToTailwindMigrator();
    $migrator->migrate($targetDir);
}

