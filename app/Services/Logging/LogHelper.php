<?php

namespace App\Services\Logging;

/**
 * Log Helper Functions
 * 
 * Convenient helper functions for common logging operations
 */
class LogHelper
{
    /**
     * Get log service instance
     * 
     * @return LogService
     */
    public static function log(): LogService
    {
        return app(LogService::class);
    }

    /**
     * Quick info log
     */
    public static function info(string $message, array $context = []): void
    {
        LogService::info($message, $context);
    }

    /**
     * Quick error log
     */
    public static function error(string $message, array $context = [], ?\Throwable $exception = null): void
    {
        LogService::error($message, $context, $exception);
    }

    /**
     * Quick warning log
     */
    public static function warning(string $message, array $context = []): void
    {
        LogService::warning($message, $context);
    }

    /**
     * Quick debug log
     */
    public static function debug(string $message, array $context = []): void
    {
        LogService::debug($message, $context);
    }

    /**
     * Quick action log
     */
    public static function action(
        string $action,
        string $resource,
        $resourceId = null,
        array $context = []
    ): void {
        LogService::action($action, $resource, $resourceId, $context);
    }
}

