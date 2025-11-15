import { useState, useCallback, useRef } from 'react';
import { HybridSearchOption, SearchType, OutputFormat } from '../types/HybridSearch';

interface UseHybridSearchReturn {
    search: (
        query: string,
        searchType: SearchType,
        options?: SearchOptions
    ) => Promise<HybridSearchOption[]>;
    loading: boolean;
    error: string | null;
    clearError: () => void;
    lastQuery: string | null;
    lastResults: HybridSearchOption[];
}

interface SearchOptions {
    format?: OutputFormat;
    maxResults?: number;
    filters?: Record<string, any>;
    debounceMs?: number;
}

interface CacheEntry {
    results: HybridSearchOption[];
    timestamp: number;
    query: string;
}

export const useHybridSearch = (): UseHybridSearchReturn => {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [lastQuery, setLastQuery] = useState<string | null>(null);
    const [lastResults, setLastResults] = useState<HybridSearchOption[]>([]);

    // Cache for search results
    const cache = useRef<Map<string, CacheEntry>>(new Map());
    const debounceTimeout = useRef<NodeJS.Timeout | null>(null);

    const clearError = useCallback(() => {
        setError(null);
    }, []);

    const search = useCallback(
        async (
            query: string,
            searchType: SearchType,
            options: SearchOptions = {}
        ): Promise<HybridSearchOption[]> => {
            const {
                format = 'react-select',
                maxResults = 20,
                filters = {},
                debounceMs = 300,
            } = options;

            // Clear previous debounce timeout
            if (debounceTimeout.current) {
                clearTimeout(debounceTimeout.current);
            }

            return new Promise((resolve, reject) => {
                debounceTimeout.current = setTimeout(async () => {
                    try {
                        // Check cache first
                        const cacheKey = `${searchType}:${query}:${JSON.stringify(filters)}`;
                        const cachedEntry = cache.current.get(cacheKey);

                        if (cachedEntry && Date.now() - cachedEntry.timestamp < 300000) {
                            // 5 minutes cache
                            setLastQuery(query);
                            setLastResults(cachedEntry.results);
                            resolve(cachedEntry.results);
                            return;
                        }

                        setLoading(true);
                        setError(null);

                        // Build query parameters
                        const params = new URLSearchParams({
                            q: query.trim(),
                            format,
                            limit: maxResults.toString(),
                            ...filters,
                        });

                        // Make API request
                        const response = await fetch(`/api/hybrid-search/${searchType}?${params}`, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                Accept: 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN':
                                    document
                                        .querySelector('meta[name="csrf-token"]')
                                        ?.getAttribute('content') || '',
                            },
                            credentials: 'same-origin',
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const data = await response.json();

                        if (!data.success) {
                            throw new Error(data.message || 'Search request failed');
                        }

                        // Transform data to HybridSearchOption format
                        const results: HybridSearchOption[] = data.data.map((item: any) => ({
                            value: item.id,
                            label: item.display_text || item.name || item.text || '',
                            searchHint: item.search_hint || '',
                            category: item.category || searchType,
                            metadata: item.metadata || {},
                        }));

                        // Cache results
                        cache.current.set(cacheKey, {
                            results,
                            timestamp: Date.now(),
                            query,
                        });

                        // Clean old cache entries (keep only last 100)
                        if (cache.current.size > 100) {
                            const entries = Array.from(cache.current.entries());
                            entries.sort((a, b) => b[1].timestamp - a[1].timestamp);
                            cache.current.clear();
                            entries.slice(0, 100).forEach(([key, value]) => {
                                cache.current.set(key, value);
                            });
                        }

                        setLastQuery(query);
                        setLastResults(results);
                        setLoading(false);

                        resolve(results);
                    } catch (err) {
                        const errorMessage =
                            err instanceof Error ? err.message : 'An unexpected error occurred';
                        setError(errorMessage);
                        setLoading(false);
                        reject(err);
                    }
                }, debounceMs);
            });
        },
        []
    );

    return {
        search,
        loading,
        error,
        clearError,
        lastQuery,
        lastResults,
    };
};
