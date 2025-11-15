/**
 * Hybrid Search System - React Select Component
 *
 * Context7 Standardı: C7-HYBRID-SEARCH-COMPONENT-2025-01-30
 * Versiyon: 1.0.0
 * Son Güncelleme: 30 Ocak 2025
 * Durum: ✅ Production Ready
 */

import React, { useState, useEffect, useCallback, useMemo } from 'react';
import Select, {
    SingleValue,
    MultiValue,
    ActionMeta,
    StylesConfig,
    CSSObjectWithLabel,
    LoadingIndicatorProps,
    NoOptionsMessageProps,
} from 'react-select';
import AsyncSelect from 'react-select/async';
import {
    HybridSearchOption,
    HybridSearchProps,
    SearchType,
    SEARCH_TYPE_PLACEHOLDERS,
    ERROR_MESSAGES,
    LOADING_MESSAGES,
    NO_OPTIONS_MESSAGES,
    formatDisplayText,
    getSearchHint,
} from '../../types/HybridSearch';
import { useHybridSearch } from '../../hooks/useHybridSearch';

// Custom loading indicator component
const LoadingIndicator: React.FC<LoadingIndicatorProps<HybridSearchOption, false>> = () => (
    <div className="flex items-center justify-center p-2">
        <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-500"></div>
        <span className="ml-2 text-sm text-gray-500">{LOADING_MESSAGES.SEARCHING}</span>
    </div>
);

// Custom no options message component
const NoOptionsMessage: React.FC<NoOptionsMessageProps<HybridSearchOption, false>> = ({ inputValue }) => {
    if (!inputValue || inputValue.length < 2) {
        return (
            <div className="p-3 text-center text-gray-500">
                {NO_OPTIONS_MESSAGES.NO_SEARCH}
            </div>
        );
    }

    return (
        <div className="p-3 text-center text-gray-500">
            {NO_OPTIONS_MESSAGES.NO_RESULTS}
        </div>
    );
};

// Custom option component
const OptionComponent: React.FC<any> = ({ data, ...props }) => (
    <div {...props} className="p-2 hover:bg-gray-100 cursor-pointer">
        <div className="font-medium text-gray-900">{data.label}</div>
        <div className="text-sm text-gray-500">{getSearchHint(data, data.searchType)}</div>
    </div>
);

// Custom styles for the select component
const customStyles: StylesConfig<HybridSearchOption, false> = {
    control: (base: CSSObjectWithLabel, state) => ({
        ...base,
        minHeight: '42px',
        border: state.isFocused ? '2px solid #3b82f6' : '2px solid #e5e7eb',
        borderRadius: '8px',
        boxShadow: state.isFocused ? '0 0 0 3px rgba(59, 130, 246, 0.1)' : 'none',
        '&:hover': {
            borderColor: state.isFocused ? '#3b82f6' : '#d1d5db',
        },
    }),
    valueContainer: (base: CSSObjectWithLabel) => ({
        ...base,
        padding: '0 12px',
    }),
    input: (base: CSSObjectWithLabel) => ({
        ...base,
        margin: '0',
        padding: '0',
    }),
    placeholder: (base: CSSObjectWithLabel) => ({
        ...base,
        color: '#9ca3af',
        fontSize: '14px',
    }),
    singleValue: (base: CSSObjectWithLabel) => ({
        ...base,
        color: '#1f2937',
        fontSize: '14px',
    }),
    option: (base: CSSObjectWithLabel, state) => ({
        ...base,
        backgroundColor: state.isSelected
            ? '#dbeafe'
            : state.isFocused
            ? '#f3f4f6'
            : 'white',
        color: state.isSelected ? '#1e40af' : '#1f2937',
        padding: '8px 12px',
        '&:hover': {
            backgroundColor: state.isSelected ? '#dbeafe' : '#f3f4f6',
        },
    }),
    menu: (base: CSSObjectWithLabel) => ({
        ...base,
        borderRadius: '8px',
        boxShadow: '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
        border: '1px solid #e5e7eb',
        marginTop: '4px',
    }),
    menuList: (base: CSSObjectWithLabel) => ({
        ...base,
        maxHeight: '300px',
        padding: '4px',
    }),
    loadingMessage: (base: CSSObjectWithLabel) => ({
        ...base,
        color: '#6b7280',
        fontSize: '14px',
        padding: '12px',
        textAlign: 'center',
    }),
    noOptionsMessage: (base: CSSObjectWithLabel) => ({
        ...base,
        color: '#6b7280',
        fontSize: '14px',
        padding: '12px',
        textAlign: 'center',
    }),
    clearIndicator: (base: CSSObjectWithLabel) => ({
        ...base,
        color: '#6b7280',
        '&:hover': {
            color: '#374151',
        },
    }),
    dropdownIndicator: (base: CSSObjectWithLabel) => ({
        ...base,
        color: '#6b7280',
        '&:hover': {
            color: '#374151',
        },
    }),
};

// Main React Select Component
const HybridSearchReactSelect: React.FC<HybridSearchProps> = ({
    searchType,
    onSelect,
    placeholder,
    isClearable = true,
    value,
    className = '',
    isDisabled = false,
    isMulti = false,
    closeMenuOnSelect = true,
    maxResults = 20,
    debounceMs = 300,
    loadingMessage = LOADING_MESSAGES.SEARCHING,
    noOptionsMessage,
    errorMessage,
}) => {
    const [selectedOption, setSelectedOption] = useState<HybridSearchOption | null>(null);
    const [inputValue, setInputValue] = useState('');

    const {
        options,
        loading,
        error,
        search,
        clear: clearSearch,
    } = useHybridSearch({
        searchType,
        config: {
            defaultLimit: maxResults,
            debounceMs,
        },
        onError: (errorMsg) => {
            console.error('Hybrid search error:', errorMsg);
        },
        onSuccess: (options) => {
            console.log(`Found ${options.length} ${searchType} options`);
        },
    });

    // Update selected option when value prop changes
    useEffect(() => {
        if (value && options.length > 0) {
            const option = options.find(opt => opt.value === value);
            setSelectedOption(option || null);
        } else if (!value) {
            setSelectedOption(null);
        }
    }, [value, options]);

    // Handle input change with debounced search
    const handleInputChange = useCallback((newValue: string, actionMeta: ActionMeta<HybridSearchOption>) => {
        setInputValue(newValue);

        if (newValue.length >= 2) {
            search(newValue);
        } else if (newValue.length === 0) {
            clearSearch();
        }
    }, [search, clearSearch]);

    // Handle option selection
    const handleChange = useCallback((
        newValue: SingleValue<HybridSearchOption>,
        actionMeta: ActionMeta<HybridSearchOption>
    ) => {
        setSelectedOption(newValue);
        onSelect(newValue);

        if (actionMeta.action === 'clear') {
            setInputValue('');
            clearSearch();
        }
    }, [onSelect, clearSearch]);

    // Load options function for AsyncSelect
    const loadOptions = useCallback(async (inputValue: string): Promise<HybridSearchOption[]> => {
        if (inputValue.length < 2) {
            return [];
        }

        try {
            const params = new URLSearchParams({
                q: inputValue,
                format: 'react-select',
                limit: maxResults.toString(),
            });

            const response = await fetch(
                `/api/hybrid-search/${searchType}?${params.toString()}`,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                }
            );

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            // Handle different response formats
            if (Array.isArray(data)) {
                return data.map((item: any) => ({
                    ...item,
                    searchType, // Add search type for hint generation
                }));
            }

            return [];
        } catch (error) {
            console.error('Load options error:', error);
            return [];
        }
    }, [searchType, maxResults]);

    // Memoized placeholder
    const memoizedPlaceholder = useMemo(() => {
        return placeholder || SEARCH_TYPE_PLACEHOLDERS[searchType];
    }, [placeholder, searchType]);

    // Memoized no options message
    const memoizedNoOptionsMessage = useMemo(() => {
        if (noOptionsMessage) return () => noOptionsMessage;
        if (error) return () => errorMessage || ERROR_MESSAGES.UNKNOWN_ERROR;
        return NoOptionsMessage;
    }, [noOptionsMessage, error, errorMessage]);

    // Memoized loading message
    const memoizedLoadingMessage = useMemo(() => {
        return () => loadingMessage;
    }, [loadingMessage]);

    return (
        <div className={`hybrid-search-react-select ${className}`}>
            <AsyncSelect
                value={selectedOption}
                onChange={handleChange}
                onInputChange={handleInputChange}
                loadOptions={loadOptions}
                placeholder={memoizedPlaceholder}
                isClearable={isClearable}
                isDisabled={isDisabled}
                isMulti={isMulti}
                closeMenuOnSelect={closeMenuOnSelect}
                isLoading={loading}
                loadingMessage={memoizedLoadingMessage}
                noOptionsMessage={memoizedNoOptionsMessage}
                styles={customStyles}
                components={{
                    LoadingIndicator,
                    Option: OptionComponent,
                    NoOptionsMessage: memoizedNoOptionsMessage,
                }}
                cacheOptions
                defaultOptions
                filterOption={null} // Disable client-side filtering
                getOptionLabel={(option) => formatDisplayText(option)}
                getOptionValue={(option) => option.value.toString()}
                className="react-select-container"
                classNamePrefix="react-select"
                aria-label={`${searchType} seçimi`}
                aria-describedby={error ? `${searchType}-error` : undefined}
            />

            {error && (
                <div
                    id={`${searchType}-error`}
                    className="mt-1 text-sm text-red-600"
                    role="alert"
                    aria-live="polite"
                >
                    {errorMessage || error}
                </div>
            )}

            {/* Debug info in development */}
            {process.env.NODE_ENV === 'development' && (
                <div className="mt-2 text-xs text-gray-400">
                    Debug: {searchType} | Options: {options.length} | Loading: {loading.toString()} | Error: {error || 'none'}
                </div>
            )}
        </div>
    );
};

// Export with default props
export default React.memo(HybridSearchReactSelect);

// Named exports for specific use cases
export const PersonSelector: React.FC<Omit<HybridSearchProps, 'searchType'>> = (props) => (
    <HybridSearchReactSelect {...props} searchType="kisiler" />
);

export const ConsultantSelector: React.FC<Omit<HybridSearchProps, 'searchType'>> = (props) => (
    <HybridSearchReactSelect {...props} searchType="danismanlar" />
);

export const SiteSelector: React.FC<Omit<HybridSearchProps, 'searchType'>> = (props) => (
    <HybridSearchReactSelect {...props} searchType="sites" />
);

// Multi-select variants
export const MultiPersonSelector: React.FC<Omit<HybridSearchProps, 'searchType' | 'isMulti'>> = (props) => (
    <HybridSearchReactSelect {...props} searchType="kisiler" isMulti={true} />
);

export const MultiConsultantSelector: React.FC<Omit<HybridSearchProps, 'searchType' | 'isMulti'>> = (props) => (
    <HybridSearchReactSelect {...props} searchType="danismanlar" isMulti={true} />
);

export const MultiSiteSelector: React.FC<Omit<HybridSearchProps, 'searchType' | 'isMulti'>> = (props) => (
    <HybridSearchReactSelect {...props} searchType="sites" isMulti={true} />
);
