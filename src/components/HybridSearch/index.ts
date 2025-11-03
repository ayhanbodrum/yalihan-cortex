/**
 * Hybrid Search System - React Components Index
 * 
 * Context7 Standardı: C7-HYBRID-SEARCH-INDEX-2025-01-30
 * Versiyon: 1.0.0
 * Son Güncelleme: 30 Ocak 2025
 * Durum: ✅ Production Ready
 */

// Main components
export { default as HybridSearchReactSelect } from './ReactSelectSearch';
export { default as HybridSearchDemo } from './HybridSearchDemo';

// Named exports for specific use cases
export {
    PersonSelector,
    ConsultantSelector,
    SiteSelector,
    MultiPersonSelector,
    MultiConsultantSelector,
    MultiSiteSelector,
} from './ReactSelectSearch';

// Types and interfaces
export type {
    HybridSearchOption,
    HybridSearchProps,
    SearchType,
    OutputFormat,
    HybridSearchResponse,
    Select2Response,
    Context7Response,
    HybridSearchError,
    UseHybridSearchReturn,
    HybridSearchConfig,
    PartialHybridSearchProps,
    SearchTypeSpecificProps,
    HybridSearchApiResponse,
} from '../../types/HybridSearch';

// Constants and utilities
export {
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
} from '../../types/HybridSearch';

// Hooks
export { default as useHybridSearch } from '../../hooks/useHybridSearch';
export { useSimpleHybridSearch, usePaginatedHybridSearch } from '../../hooks/useHybridSearch';

// Re-export everything for convenience
export * from '../../types/HybridSearch';
export * from '../../hooks/useHybridSearch';
