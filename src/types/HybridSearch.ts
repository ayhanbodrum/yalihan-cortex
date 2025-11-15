/**
 * Hybrid Search System - TypeScript Type Definitions
 *
 * Context7 Standardı: C7-HYBRID-SEARCH-TYPES-2025-01-30
 * Versiyon: 1.0.0
 * Son Güncelleme: 30 Ocak 2025
 * Durum: ✅ Production Ready
 */

// Base search option interface
export interface HybridSearchOption {
    value: number;
    label: string;
    data: {
        id: number;
        name?: string;
        ad?: string;
        soyad?: string;
        email: string;
        status: boolean | string;
        roles?: string[];
        // Additional fields for different types
        telefon?: string;
        address?: string;
        description?: string;
    };
}

// Search type definition
export type SearchType = 'kisiler' | 'danismanlar' | 'sites';

// Output format definition
export type OutputFormat = 'select2' | 'context7' | 'react-select';

// React Select component props
export interface HybridSearchProps {
    searchType: SearchType;
    onSelect: (option: HybridSearchOption | null) => void;
    placeholder?: string;
    isClearable?: boolean;
    value?: number;
    className?: string;
    isDisabled?: boolean;
    isMulti?: boolean;
    closeMenuOnSelect?: boolean;
    maxResults?: number;
    debounceMs?: number;
    loadingMessage?: string;
    noOptionsMessage?: string;
    errorMessage?: string;
}

// API response interfaces
export interface HybridSearchResponse {
    success: boolean;
    count: number;
    data: HybridSearchOption[];
    search_metadata: {
        query: string;
        type: SearchType;
        context7_compliant: boolean;
        hybrid_api: boolean;
    };
}

// Select2 format response
export interface Select2Response {
    results: Array<{
        id: number;
        text: string;
    }>;
    pagination: {
        more: boolean;
    };
}

// Context7 format response
export interface Context7Response {
    success: boolean;
    count: number;
    data: Array<{
        id: number;
        display_text: string;
        search_hint: string;
        data: HybridSearchOption['data'];
    }>;
    search_metadata: {
        query: string;
        type: SearchType;
        context7_compliant: boolean;
        hybrid_api: boolean;
    };
}

// Error interface
export interface HybridSearchError {
    success: false;
    error: string;
    message: string;
    timestamp: string;
}

// Search hook return type
export interface UseHybridSearchReturn {
    options: HybridSearchOption[];
    loading: boolean;
    error: string | null;
    search: (query: string) => Promise<void>;
    clear: () => void;
    hasMore: boolean;
    loadMore: () => Promise<void>;
}

// Configuration interface
export interface HybridSearchConfig {
    apiBaseUrl: string;
    defaultLimit: number;
    debounceMs: number;
    timeoutMs: number;
    retryAttempts: number;
    enableCache: boolean;
    cacheTtlMs: number;
}

// Default configuration
export const DEFAULT_CONFIG: HybridSearchConfig = {
    apiBaseUrl: '/api/hybrid-search',
    defaultLimit: 20,
    debounceMs: 300,
    timeoutMs: 10000,
    retryAttempts: 3,
    enableCache: true,
    cacheTtlMs: 300000, // 5 minutes
};

// Search type labels
export const SEARCH_TYPE_LABELS: Record<SearchType, string> = {
    kisiler: 'Kişi',
    danismanlar: 'Danışman',
    sites: 'Site/Apartman',
};

// Search type placeholders
export const SEARCH_TYPE_PLACEHOLDERS: Record<SearchType, string> = {
    kisiler: 'Kişi ara ve seç...',
    danismanlar: 'Danışman ara ve seç...',
    sites: 'Site/Apartman ara ve seç...',
};

// Error messages
export const ERROR_MESSAGES = {
    NETWORK_ERROR: 'Ağ bağlantısı hatası. Lütfen internet bağlantınızı kontrol edin.',
    TIMEOUT_ERROR: 'İstek zaman aşımına uğradı. Lütfen tekrar deneyin.',
    VALIDATION_ERROR: 'Geçersiz arama sorgusu.',
    SERVER_ERROR: 'Sunucu hatası. Lütfen daha sonra tekrar deneyin.',
    UNKNOWN_ERROR: 'Bilinmeyen hata oluştu.',
    MIN_LENGTH_ERROR: 'Arama sorgusu en az 2 karakter olmalı.',
    MAX_LENGTH_ERROR: 'Arama sorgusu en fazla 100 karakter olabilir.',
} as const;

// Success messages
export const SUCCESS_MESSAGES = {
    SEARCH_COMPLETE: 'Arama tamamlandı',
    OPTION_SELECTED: 'Seçim yapıldı',
    CLEARED: 'Temizlendi',
} as const;

// Loading messages
export const LOADING_MESSAGES = {
    SEARCHING: 'Aranıyor...',
    LOADING_MORE: 'Daha fazla yükleniyor...',
    INITIALIZING: 'Başlatılıyor...',
} as const;

// No options messages
export const NO_OPTIONS_MESSAGES = {
    NO_SEARCH: 'Arama yapmak için en az 2 karakter girin',
    NO_RESULTS: 'Sonuç bulunamadı',
    NO_MORE_RESULTS: 'Başka sonuç yok',
    SEARCH_ERROR: 'Arama sırasında hata oluştu',
} as const;

// Utility type for partial updates
export type PartialHybridSearchProps = Partial<HybridSearchProps>;

// Utility type for search type specific props
export type SearchTypeSpecificProps<T extends SearchType> = HybridSearchProps & {
    searchType: T;
};

// Union type for all possible response formats
export type HybridSearchApiResponse =
    | HybridSearchResponse
    | Select2Response
    | Context7Response
    | HybridSearchError;

// Type guard functions
export function isHybridSearchResponse(response: any): response is HybridSearchResponse {
    return response && typeof response === 'object' && response.success === true;
}

export function isSelect2Response(response: any): response is Select2Response {
    return response && typeof response === 'object' && Array.isArray(response.results);
}

export function isContext7Response(response: any): response is Context7Response {
    return (
        response &&
        typeof response === 'object' &&
        response.success === true &&
        Array.isArray(response.data)
    );
}

export function isHybridSearchError(response: any): response is HybridSearchError {
    return response && typeof response === 'object' && response.success === false;
}

// Validation functions
export function validateSearchQuery(query: string): { isValid: boolean; error?: string } {
    if (!query || typeof query !== 'string') {
        return { isValid: false, error: ERROR_MESSAGES.VALIDATION_ERROR };
    }

    if (query.length < 2) {
        return { isValid: false, error: ERROR_MESSAGES.MIN_LENGTH_ERROR };
    }

    if (query.length > 100) {
        return { isValid: false, error: ERROR_MESSAGES.MAX_LENGTH_ERROR };
    }

    return { isValid: true };
}

export function validateSearchType(type: string): type is SearchType {
    return ['kisiler', 'danismanlar', 'sites'].includes(type);
}

// Helper functions
export function formatDisplayText(option: HybridSearchOption): string {
    const { data } = option;

    if (data.ad && data.soyad) {
        // Kişi formatı
        return `${data.ad} ${data.soyad} (${data.email})`;
    } else if (data.name) {
        // Danışman veya Site formatı
        return `${data.name} (${data.email || data.address || ''})`;
    } else {
        return option.label;
    }
}

export function getSearchHint(option: HybridSearchOption, searchType: SearchType): string {
    const { data } = option;
    const typeLabel = SEARCH_TYPE_LABELS[searchType];

    if (data.status === true || data.status === 'Aktif') {
        return `${typeLabel} • Aktif`;
    } else {
        return `${typeLabel} • Pasif`;
    }
}

// Export all types and interfaces
export default {
    HybridSearchOption,
    SearchType,
    OutputFormat,
    HybridSearchProps,
    HybridSearchResponse,
    Select2Response,
    Context7Response,
    HybridSearchError,
    UseHybridSearchReturn,
    HybridSearchConfig,
    DEFAULT_CONFIG,
    SEARCH_TYPE_LABELS,
    SEARCH_TYPE_PLACEHOLDERS,
    ERROR_MESSAGES,
    SUCCESS_MESSAGES,
    LOADING_MESSAGES,
    NO_OPTIONS_MESSAGES,
    isHybridSearchResponse,
    isSelect2Response,
    isContext7Response,
    isHybridSearchError,
    validateSearchQuery,
    validateSearchType,
    formatDisplayText,
    getSearchHint,
};
