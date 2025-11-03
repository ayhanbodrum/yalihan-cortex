/**
 * Hybrid Search System - Error Handler Utilities
 *
 * Context7 Standardı: C7-HYBRID-SEARCH-ERROR-HANDLER-2025-01-30
 * Versiyon: 1.0.0
 * Son Güncelleme: 30 Ocak 2025
 * Durum: ✅ Production Ready
 */

import { ERROR_MESSAGES } from '../types/HybridSearch';

// Error types
export enum ErrorType {
    NETWORK_ERROR = 'NETWORK_ERROR',
    TIMEOUT_ERROR = 'TIMEOUT_ERROR',
    VALIDATION_ERROR = 'VALIDATION_ERROR',
    SERVER_ERROR = 'SERVER_ERROR',
    PARSE_ERROR = 'PARSE_ERROR',
    UNKNOWN_ERROR = 'UNKNOWN_ERROR',
}

// Error severity levels
export enum ErrorSeverity {
    LOW = 'LOW',
    MEDIUM = 'MEDIUM',
    HIGH = 'HIGH',
    CRITICAL = 'CRITICAL',
}

// Error interface
export interface HybridSearchError {
    type: ErrorType;
    severity: ErrorSeverity;
    message: string;
    originalError?: Error;
    timestamp: string;
    context?: Record<string, any>;
    retryable: boolean;
    userMessage: string;
}

// Error handler class
export class ErrorHandler {
    private static instance: ErrorHandler;
    private errorLog: HybridSearchError[] = [];
    private maxLogSize = 100;

    private constructor() {}

    public static getInstance(): ErrorHandler {
        if (!ErrorHandler.instance) {
            ErrorHandler.instance = new ErrorHandler();
        }
        return ErrorHandler.instance;
    }

    // Handle different types of errors
    public handleError(error: unknown, context?: Record<string, any>): HybridSearchError {
        const processedError = this.processError(error, context);
        this.logError(processedError);
        return processedError;
    }

    // Process error and determine type, severity, and user message
    private processError(error: unknown, context?: Record<string, any>): HybridSearchError {
        const timestamp = new Date().toISOString();

        if (error instanceof Error) {
            return this.processStandardError(error, timestamp, context);
        }

        if (typeof error === 'string') {
            return this.processStringError(error, timestamp, context);
        }

        if (typeof error === 'object' && error !== null) {
            return this.processObjectError(error, timestamp, context);
        }

        return this.processUnknownError(error, timestamp, context);
    }

    // Process standard Error objects
    private processStandardError(error: Error, timestamp: string, context?: Record<string, any>): HybridSearchError {
        const message = error.message;
        const name = error.name;

        // Network errors
        if (name === 'AbortError' || message.includes('fetch')) {
            return {
                type: ErrorType.NETWORK_ERROR,
                severity: ErrorSeverity.MEDIUM,
                message,
                originalError: error,
                timestamp,
                context,
                retryable: true,
                userMessage: ERROR_MESSAGES.NETWORK_ERROR,
            };
        }

        // Timeout errors
        if (name === 'TimeoutError' || message.includes('timeout')) {
            return {
                type: ErrorType.TIMEOUT_ERROR,
                severity: ErrorSeverity.MEDIUM,
                message,
                originalError: error,
                timestamp,
                context,
                retryable: true,
                userMessage: ERROR_MESSAGES.TIMEOUT_ERROR,
            };
        }

        // Validation errors
        if (message.includes('validation') || message.includes('invalid')) {
            return {
                type: ErrorType.VALIDATION_ERROR,
                severity: ErrorSeverity.LOW,
                message,
                originalError: error,
                timestamp,
                context,
                retryable: false,
                userMessage: ERROR_MESSAGES.VALIDATION_ERROR,
            };
        }

        // Default to unknown error
        return {
            type: ErrorType.UNKNOWN_ERROR,
            severity: ErrorSeverity.HIGH,
            message,
            originalError: error,
            timestamp,
            context,
            retryable: false,
            userMessage: ERROR_MESSAGES.UNKNOWN_ERROR,
        };
    }

    // Process string errors
    private processStringError(error: string, timestamp: string, context?: Record<string, any>): HybridSearchError {
        return {
            type: ErrorType.UNKNOWN_ERROR,
            severity: ErrorSeverity.MEDIUM,
            message: error,
            timestamp,
            context,
            retryable: false,
            userMessage: ERROR_MESSAGES.UNKNOWN_ERROR,
        };
    }

    // Process object errors
    private processObjectError(error: object, timestamp: string, context?: Record<string, any>): HybridSearchError {
        const message = JSON.stringify(error);
        return {
            type: ErrorType.UNKNOWN_ERROR,
            severity: ErrorSeverity.MEDIUM,
            message,
            timestamp,
            context,
            retryable: false,
            userMessage: ERROR_MESSAGES.UNKNOWN_ERROR,
        };
    }

    // Process unknown errors
    private processUnknownError(error: unknown, timestamp: string, context?: Record<string, any>): HybridSearchError {
        const message = String(error);
        return {
            type: ErrorType.UNKNOWN_ERROR,
            severity: ErrorSeverity.HIGH,
            message,
            timestamp,
            context,
            retryable: false,
            userMessage: ERROR_MESSAGES.UNKNOWN_ERROR,
        };
    }

    // Log error to internal log
    private logError(error: HybridSearchError): void {
        this.errorLog.push(error);

        // Keep log size manageable
        if (this.errorLog.length > this.maxLogSize) {
            this.errorLog = this.errorLog.slice(-this.maxLogSize);
        }

        // Log to console in development
        if (process.env.NODE_ENV === 'development') {
            console.error('Hybrid Search Error:', error);
        }
    }

    // Get error log
    public getErrorLog(): HybridSearchError[] {
        return [...this.errorLog];
    }

    // Clear error log
    public clearErrorLog(): void {
        this.errorLog = [];
    }

    // Get errors by type
    public getErrorsByType(type: ErrorType): HybridSearchError[] {
        return this.errorLog.filter(error => error.type === type);
    }

    // Get errors by severity
    public getErrorsBySeverity(severity: ErrorSeverity): HybridSearchError[] {
        return this.errorLog.filter(error => error.severity === severity);
    }

    // Get retryable errors
    public getRetryableErrors(): HybridSearchError[] {
        return this.errorLog.filter(error => error.retryable);
    }

    // Get error statistics
    public getErrorStats(): Record<string, number> {
        const stats: Record<string, number> = {};

        this.errorLog.forEach(error => {
            stats[error.type] = (stats[error.type] || 0) + 1;
        });

        return stats;
    }
}

// Utility functions for common error scenarios
export const createNetworkError = (message: string, context?: Record<string, any>): HybridSearchError => ({
    type: ErrorType.NETWORK_ERROR,
    severity: ErrorSeverity.MEDIUM,
    message,
    timestamp: new Date().toISOString(),
    context,
    retryable: true,
    userMessage: ERROR_MESSAGES.NETWORK_ERROR,
});

export const createTimeoutError = (message: string, context?: Record<string, any>): HybridSearchError => ({
    type: ErrorType.TIMEOUT_ERROR,
    severity: ErrorSeverity.MEDIUM,
    message,
    timestamp: new Date().toISOString(),
    context,
    retryable: true,
    userMessage: ERROR_MESSAGES.TIMEOUT_ERROR,
});

export const createValidationError = (message: string, context?: Record<string, any>): HybridSearchError => ({
    type: ErrorType.VALIDATION_ERROR,
    severity: ErrorSeverity.LOW,
    message,
    timestamp: new Date().toISOString(),
    context,
    retryable: false,
    userMessage: ERROR_MESSAGES.VALIDATION_ERROR,
});

export const createServerError = (message: string, context?: Record<string, any>): HybridSearchError => ({
    type: ErrorType.SERVER_ERROR,
    severity: ErrorSeverity.HIGH,
    message,
    timestamp: new Date().toISOString(),
    context,
    retryable: true,
    userMessage: ERROR_MESSAGES.SERVER_ERROR,
});

export const createParseError = (message: string, context?: Record<string, any>): HybridSearchError => ({
    type: ErrorType.PARSE_ERROR,
    severity: ErrorSeverity.HIGH,
    message,
    timestamp: new Date().toISOString(),
    context,
    retryable: false,
    userMessage: ERROR_MESSAGES.UNKNOWN_ERROR,
});

// Error boundary component for React
export class HybridSearchErrorBoundary extends React.Component<
    { children: React.ReactNode; fallback?: React.ComponentType<{ error: HybridSearchError }> },
    { hasError: boolean; error: HybridSearchError | null }
> {
    constructor(props: { children: React.ReactNode; fallback?: React.ComponentType<{ error: HybridSearchError }> }) {
        super(props);
        this.state = { hasError: false, error: null };
    }

    static getDerivedStateFromError(error: Error): { hasError: boolean; error: HybridSearchError } {
        const errorHandler = ErrorHandler.getInstance();
        const processedError = errorHandler.handleError(error, { component: 'HybridSearchErrorBoundary' });

        return {
            hasError: true,
            error: processedError,
        };
    }

    componentDidCatch(error: Error, errorInfo: React.ErrorInfo) {
        const errorHandler = ErrorHandler.getInstance();
        errorHandler.handleError(error, {
            component: 'HybridSearchErrorBoundary',
            errorInfo,
        });
    }

    render() {
        if (this.state.hasError && this.state.error) {
            if (this.props.fallback) {
                const FallbackComponent = this.props.fallback;
                return <FallbackComponent error={this.state.error} />;
            }

            return (
                <div className="p-4 bg-red-50 border border-red-200 rounded-md">
                    <div className="flex">
                        <div className="ml-3">
                            <h3 className="text-sm font-medium text-red-800">
                                Hybrid Search Hatası
                            </h3>
                            <div className="mt-2 text-sm text-red-700">
                                <p>{this.state.error.userMessage}</p>
                                {process.env.NODE_ENV === 'development' && (
                                    <details className="mt-2">
                                        <summary className="cursor-pointer">Teknik Detaylar</summary>
                                        <pre className="mt-2 text-xs bg-red-100 p-2 rounded overflow-auto">
                                            {JSON.stringify(this.state.error, null, 2)}
                                        </pre>
                                    </details>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            );
        }

        return this.props.children;
    }
}

// Default error fallback component
export const DefaultErrorFallback: React.FC<{ error: HybridSearchError }> = ({ error }) => (
    <div className="p-4 bg-red-50 border border-red-200 rounded-md">
        <div className="flex">
            <div className="ml-3">
                <h3 className="text-sm font-medium text-red-800">
                    Arama Sırasında Hata Oluştu
                </h3>
                <div className="mt-2 text-sm text-red-700">
                    <p>{error.userMessage}</p>
                    {error.retryable && (
                        <button
                            onClick={() => window.location.reload()}
                            className="mt-2 px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700"
                        >
                            Tekrar Dene
                        </button>
                    )}
                </div>
            </div>
        </div>
    </div>
);

// Export singleton instance
export const errorHandler = ErrorHandler.getInstance();

// Export everything
export default errorHandler;
