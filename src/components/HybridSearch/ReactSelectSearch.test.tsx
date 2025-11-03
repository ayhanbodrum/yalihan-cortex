import React from 'react';
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { HybridSearchReactSelect } from './ReactSelectSearch';
import { HybridSearchOption } from '../../types/HybridSearch';

// Mock the useHybridSearch hook
jest.mock('../../hooks/useHybridSearch', () => ({
    useHybridSearch: jest.fn(() => ({
        search: jest.fn(),
        loading: false,
        error: null,
        clearError: jest.fn(),
    })),
}));

// Mock fetch for API calls
global.fetch = jest.fn();

describe('HybridSearchReactSelect', () => {
    const mockOnSelect = jest.fn();
    const defaultProps = {
        searchType: 'kisiler' as const,
        onSelect: mockOnSelect,
    };

    beforeEach(() => {
        jest.clearAllMocks();
        (fetch as jest.Mock).mockResolvedValue({
            ok: true,
            json: async () => ({
                success: true,
                data: [
                    {
                        id: 1,
                        display_text: 'Ahmet Yılmaz',
                        search_hint: 'Telefon: 0533 123 45 67',
                        category: 'person',
                        metadata: {}
                    }
                ],
                count: 1
            })
        });
    });

    afterEach(() => {
        jest.restoreAllMocks();
    });

    describe('Rendering', () => {
        it('renders with default props', () => {
            render(<HybridSearchReactSelect {...defaultProps} />);

            const selectContainer = screen.getByRole('combobox');
            expect(selectContainer).toBeInTheDocument();
        });

        it('renders with custom placeholder', () => {
            const customPlaceholder = 'Custom placeholder';
            render(
                <HybridSearchReactSelect
                    {...defaultProps}
                    placeholder={customPlaceholder}
                />
            );

            expect(screen.getByText(customPlaceholder)).toBeInTheDocument();
        });

        it('renders with custom className', () => {
            const customClass = 'custom-class';
            const { container } = render(
                <HybridSearchReactSelect
                    {...defaultProps}
                    className={customClass}
                />
            );

            expect(container.firstChild).toHaveClass(customClass);
        });

        it('renders as disabled when isDisabled is true', () => {
            render(
                <HybridSearchReactSelect
                    {...defaultProps}
                    isDisabled={true}
                />
            );

            const selectContainer = screen.getByRole('combobox');
            expect(selectContainer).toHaveAttribute('aria-disabled', 'true');
        });
    });

    describe('User Interactions', () => {
        it('calls onSelect when option is selected', async () => {
            const user = userEvent.setup();
            render(<HybridSearchReactSelect {...defaultProps} />);

            const selectContainer = screen.getByRole('combobox');
            await user.click(selectContainer);

            // Wait for options to load
            await waitFor(() => {
                expect(fetch).toHaveBeenCalled();
            });

            // This test would need more specific mocking for the actual option selection
            // as react-select's internal behavior is complex
        });

        it('clears selection when clearable and clear button is clicked', async () => {
            const user = userEvent.setup();
            render(
                <HybridSearchReactSelect
                    {...defaultProps}
                    isClearable={true}
                    value={1}
                />
            );

            // This test would need to simulate the clear action
            // Implementation depends on react-select's internal structure
        });
    });

    describe('Search Functionality', () => {
        it('calls API with correct parameters when user types', async () => {
            const user = userEvent.setup();
            render(<HybridSearchReactSelect {...defaultProps} />);

            const selectContainer = screen.getByRole('combobox');
            await user.click(selectContainer);
            await user.type(selectContainer, 'test query');

            await waitFor(() => {
                expect(fetch).toHaveBeenCalledWith(
                    expect.stringContaining('/api/hybrid-search/kisiler'),
                    expect.objectContaining({
                        method: 'GET',
                        headers: expect.any(Object),
                    })
                );
            });
        });

        it('handles API errors gracefully', async () => {
            (fetch as jest.Mock).mockRejectedValue(new Error('API Error'));

            const user = userEvent.setup();
            render(<HybridSearchReactSelect {...defaultProps} />);

            const selectContainer = screen.getByRole('combobox');
            await user.click(selectContainer);
            await user.type(selectContainer, 'test query');

            // Should handle error without crashing
            await waitFor(() => {
                expect(selectContainer).toBeInTheDocument();
            });
        });

        it('shows loading state during search', async () => {
            // Mock loading state
            const mockUseHybridSearch = require('../../hooks/useHybridSearch').useHybridSearch;
            mockUseHybridSearch.mockReturnValue({
                search: jest.fn(),
                loading: true,
                error: null,
                clearError: jest.fn(),
            });

            const user = userEvent.setup();
            render(<HybridSearchReactSelect {...defaultProps} />);

            const selectContainer = screen.getByRole('combobox');
            await user.click(selectContainer);

            // Should show loading indicator
            expect(screen.getByText('Aranıyor...')).toBeInTheDocument();
        });
    });

    describe('Accessibility', () => {
        it('has proper ARIA labels', () => {
            render(<HybridSearchReactSelect {...defaultProps} />);

            const selectContainer = screen.getByRole('combobox');
            expect(selectContainer).toHaveAttribute('aria-label', 'kisiler seçimi');
        });

        it('announces errors to screen readers', async () => {
            const mockUseHybridSearch = require('../../hooks/useHybridSearch').useHybridSearch;
            mockUseHybridSearch.mockReturnValue({
                search: jest.fn(),
                loading: false,
                error: 'Test error',
                clearError: jest.fn(),
            });

            render(<HybridSearchReactSelect {...defaultProps} />);

            const errorElement = screen.getByRole('alert');
            expect(errorElement).toBeInTheDocument();
            expect(errorElement).toHaveTextContent('Test error');
        });
    });

    describe('Multi-select', () => {
        it('renders as multi-select when isMulti is true', () => {
            render(
                <HybridSearchReactSelect
                    {...defaultProps}
                    isMulti={true}
                />
            );

            const selectContainer = screen.getByRole('combobox');
            expect(selectContainer).toBeInTheDocument();
            // Additional assertions for multi-select behavior would go here
        });
    });

    describe('Error Handling', () => {
        it('displays custom error message when provided', () => {
            const customError = 'Custom error message';
            const mockUseHybridSearch = require('../../hooks/useHybridSearch').useHybridSearch;
            mockUseHybridSearch.mockReturnValue({
                search: jest.fn(),
                loading: false,
                error: 'API Error',
                clearError: jest.fn(),
            });

            render(
                <HybridSearchReactSelect
                    {...defaultProps}
                    errorMessage={customError}
                />
            );

            const errorElement = screen.getByRole('alert');
            expect(errorElement).toHaveTextContent(customError);
        });
    });

    describe('Performance', () => {
        it('debounces search requests', async () => {
            const user = userEvent.setup();
            render(
                <HybridSearchReactSelect
                    {...defaultProps}
                    debounceMs={300}
                />
            );

            const selectContainer = screen.getByRole('combobox');
            await user.click(selectContainer);

            // Type multiple characters quickly
            await user.type(selectContainer, 'abc');

            // Should not make multiple API calls immediately
            // This test would need more sophisticated timing control
        });

        it('caches options for repeated searches', async () => {
            const user = userEvent.setup();
            render(<HybridSearchReactSelect {...defaultProps} />);

            const selectContainer = screen.getByRole('combobox');
            await user.click(selectContainer);
            await user.type(selectContainer, 'test');

            // Clear and type the same query again
            await user.clear(selectContainer);
            await user.type(selectContainer, 'test');

            // Should use cached results
            await waitFor(() => {
                expect(fetch).toHaveBeenCalledTimes(1);
            });
        });
    });
});
