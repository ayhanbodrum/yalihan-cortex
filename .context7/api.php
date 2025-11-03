<?php

/**
 * Context7 Central Authority API
 *
 * Bu dosya, IDE'ler ve dış araçlar tarafından Context7 kurallarına
 * programmatik olarak erişim sağlamak için kullanılır.
 *
 * Usage:
 * php .context7/api.php get-forbidden-patterns
 * php .context7/api.php validate-field status
 * php .context7/api.php get-replacement status
 */

class Context7AuthorityAPI
{
    private $authorityData;
    private $authorityFile;

    public function __construct()
    {
        $this->authorityFile = dirname(__FILE__) . '/authority.json';
        $this->loadAuthorityData();
    }

    private function loadAuthorityData()
    {
        if (!file_exists($this->authorityFile)) {
            throw new Exception("Authority file not found: " . $this->authorityFile);
        }

        $content = file_get_contents($this->authorityFile);
        $this->authorityData = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON in authority file: " . json_last_error_msg());
        }
    }

    /**
     * Get all forbidden patterns
     */
    public function getForbiddenPatterns(): array
    {
        return $this->authorityData['forbidden_patterns'] ?? [];
    }

    /**
     * Get forbidden database fields
     */
    public function getForbiddenDatabaseFields(): array
    {
        return $this->authorityData['forbidden_patterns']['database_fields'] ?? [];
    }

    /**
     * Get forbidden model relationships
     */
    public function getForbiddenModelRelationships(): array
    {
        return $this->authorityData['forbidden_patterns']['model_relationships'] ?? [];
    }

    /**
     * Get forbidden CSS classes
     */
    public function getForbiddenCssClasses(): array
    {
        return $this->authorityData['forbidden_patterns']['css_classes'] ?? [];
    }

    /**
     * Validate a database field name
     */
    public function validateDatabaseField(string $fieldName): array
    {
        $forbiddenFields = $this->getForbiddenDatabaseFields();

        if (isset($forbiddenFields[$fieldName])) {
            return [
                'valid' => false,
                'field' => $fieldName,
                'violation' => $forbiddenFields[$fieldName],
                'replacement' => $forbiddenFields[$fieldName]['replacement'],
                'reason' => $forbiddenFields[$fieldName]['reason'],
                'severity' => $forbiddenFields[$fieldName]['severity'],
                'auto_fix' => $forbiddenFields[$fieldName]['auto_fix']
            ];
        }

        return [
            'valid' => true,
            'field' => $fieldName,
            'message' => 'Field name is Context7 compliant'
        ];
    }

    /**
     * Get replacement for a forbidden field
     */
    public function getReplacement(string $fieldName): ?string
    {
        $forbiddenFields = $this->getForbiddenDatabaseFields();

        if (isset($forbiddenFields[$fieldName])) {
            $replacement = $forbiddenFields[$fieldName]['replacement'];
            return $replacement === 'REMOVE' ? null : $replacement;
        }

        return $fieldName; // Already compliant
    }

    /**
     * Get compliance rules
     */
    public function getComplianceRules(): array
    {
        return $this->authorityData['compliance_rules'] ?? [];
    }

    /**
     * Get IDE integration settings
     */
    public function getIdeIntegrations(): array
    {
        return $this->authorityData['ide_integrations'] ?? [];
    }

    /**
     * Get IDE-specific settings
     */
    public function getIdeSettings(string $ide): array
    {
        $integrations = $this->getIdeIntegrations();
        return $integrations[$ide] ?? [];
    }

    /**
     * Check if pattern should be auto-fixed
     */
    public function shouldAutoFix(string $pattern, string $type = 'database_fields'): bool
    {
        $patterns = $this->authorityData['forbidden_patterns'][$type] ?? [];
        return $patterns[$pattern]['auto_fix'] ?? false;
    }

    /**
     * Get severity level for a pattern
     */
    public function getSeverity(string $pattern, string $type = 'database_fields'): string
    {
        $patterns = $this->authorityData['forbidden_patterns'][$type] ?? [];
        return $patterns[$pattern]['severity'] ?? 'low';
    }

    /**
     * Get authority file metadata
     */
    public function getMetadata(): array
    {
        return $this->authorityData['context7'] ?? [];
    }

    /**
     * Get compliance targets
     */
    public function getComplianceTargets(): array
    {
        return $this->authorityData['compliance_targets'] ?? [];
    }

    /**
     * Export rules for specific IDE
     */
    public function exportForIde(string $ide): array
    {
        $ideSettings = $this->getIdeSettings($ide);
        $forbiddenPatterns = $this->getForbiddenPatterns();
        $complianceRules = $this->getComplianceRules();

        return [
            'metadata' => $this->getMetadata(),
            'ide_settings' => $ideSettings,
            'forbidden_patterns' => $forbiddenPatterns,
            'compliance_rules' => $complianceRules,
            'targets' => $this->getComplianceTargets()
        ];
    }

    /**
     * Validate multiple fields at once
     */
    public function validateMultipleFields(array $fields): array
    {
        $results = [];
        foreach ($fields as $field) {
            $results[$field] = $this->validateDatabaseField($field);
        }
        return $results;
    }

    /**
     * Get all auto-fixable violations
     */
    public function getAutoFixableViolations(): array
    {
        $autoFixable = [];

        foreach ($this->getForbiddenDatabaseFields() as $field => $rule) {
            if ($rule['auto_fix']) {
                $autoFixable[$field] = $rule;
            }
        }

        return $autoFixable;
    }

    /**
     * Generate migration suggestions
     */
    public function generateMigrationSuggestions(): array
    {
        $suggestions = [];

        foreach ($this->getForbiddenDatabaseFields() as $field => $rule) {
            if ($rule['auto_fix'] && $rule['replacement'] !== 'REMOVE') {
                $suggestions[] = [
                    'type' => 'rename_column',
                    'from' => $field,
                    'to' => $rule['replacement'],
                    'reason' => $rule['reason'],
                    'severity' => $rule['severity']
                ];
            } elseif ($rule['replacement'] === 'REMOVE') {
                $suggestions[] = [
                    'type' => 'drop_column',
                    'column' => $field,
                    'reason' => $rule['reason'],
                    'severity' => $rule['severity']
                ];
            }
        }

        return $suggestions;
    }
}

// CLI Interface
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['argv'][0])) {
    $api = new Context7AuthorityAPI();

    if (count($_SERVER['argv']) < 2) {
        echo "Context7 Authority API\n";
        echo "Usage: php api.php <command> [args]\n\n";
        echo "Commands:\n";
        echo "  get-forbidden-patterns        Get all forbidden patterns\n";
        echo "  validate-field <field>        Validate a database field\n";
        echo "  get-replacement <field>       Get replacement for field\n";
        echo "  get-compliance-rules          Get compliance rules\n";
        echo "  get-ide-settings <ide>        Get IDE-specific settings\n";
        echo "  export-for-ide <ide>          Export rules for IDE\n";
        echo "  get-migration-suggestions     Get migration suggestions\n";
        echo "  get-metadata                  Get authority metadata\n";
        exit(0);
    }

    $command = $_SERVER['argv'][1];

    try {
        switch ($command) {
            case 'get-forbidden-patterns':
                echo json_encode($api->getForbiddenPatterns(), JSON_PRETTY_PRINT);
                break;

            case 'validate-field':
                if (count($_SERVER['argv']) < 3) {
                    echo "Usage: validate-field <field_name>\n";
                    exit(1);
                }
                $field = $_SERVER['argv'][2];
                echo json_encode($api->validateDatabaseField($field), JSON_PRETTY_PRINT);
                break;

            case 'get-replacement':
                if (count($_SERVER['argv']) < 3) {
                    echo "Usage: get-replacement <field_name>\n";
                    exit(1);
                }
                $field = $_SERVER['argv'][2];
                $replacement = $api->getReplacement($field);
                echo $replacement ?? "REMOVE\n";
                break;

            case 'get-compliance-rules':
                echo json_encode($api->getComplianceRules(), JSON_PRETTY_PRINT);
                break;

            case 'get-ide-settings':
                if (count($_SERVER['argv']) < 3) {
                    echo "Usage: get-ide-settings <ide_name>\n";
                    exit(1);
                }
                $ide = $_SERVER['argv'][2];
                echo json_encode($api->getIdeSettings($ide), JSON_PRETTY_PRINT);
                break;

            case 'export-for-ide':
                if (count($_SERVER['argv']) < 3) {
                    echo "Usage: export-for-ide <ide_name>\n";
                    exit(1);
                }
                $ide = $_SERVER['argv'][2];
                echo json_encode($api->exportForIde($ide), JSON_PRETTY_PRINT);
                break;

            case 'get-migration-suggestions':
                echo json_encode($api->generateMigrationSuggestions(), JSON_PRETTY_PRINT);
                break;

            case 'get-metadata':
                echo json_encode($api->getMetadata(), JSON_PRETTY_PRINT);
                break;

            default:
                echo "Unknown command: $command\n";
                exit(1);
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

// Return API instance for require/include usage
return $api ?? new Context7AuthorityAPI();
