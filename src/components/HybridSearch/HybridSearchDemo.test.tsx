/**
 * Hybrid Search System - Demo Component Tests
 *
 * Context7 Standardı: C7-HYBRID-SEARCH-DEMO-TESTS-2025-01-30
 * Versiyon: 1.0.0
 * Son Güncelleme: 30 Ocak 2025
 * Durum: ✅ Production Ready
 */

import React from 'react';
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import '@testing-library/jest-dom';
import HybridSearchDemo from './HybridSearchDemo';

// Mock the React Select components
jest.mock('./ReactSelectSearch', () => ({
    HybridSearchReactSelect: ({ onSelect, placeholder, searchType, ...props }: any) => (
        <div data-testid={`${searchType}-selector`}>
            <input
                data-testid={`${searchType}-input`}
                placeholder={placeholder}
                onChange={(e) => {
                    if (e.target.value.length >= 2) {
                        // Simulate finding options
                        const mockOption = {
                            value: 1,
                            label: `Mock ${searchType} Option`,
                            data: { id: 1, name: 'Mock Option', email: 'mock@example.com' }
                        };
                        onSelect(mockOption);
                    }
                }}
            />
        </div>
    ),
    PersonSelector: ({ onSelect, placeholder, ...props }: any) => (
        <div data-testid="person-selector">
            <input
                data-testid="person-input"
                placeholder={placeholder}
                onChange={(e) => {
                    if (e.target.value.length >= 2) {
                        const mockOption = {
                            value: 1,
                            label: 'Mock Person Option',
                            data: { id: 1, name: 'Mock Person', email: 'person@example.com' }
                        };
                        onSelect(mockOption);
                    }
                }}
            />
        </div>
    ),
    ConsultantSelector: ({ onSelect, placeholder, ...props }: any) => (
        <div data-testid="consultant-selector">
            <input
                data-testid="consultant-input"
                placeholder={placeholder}
                onChange={(e) => {
                    if (e.target.value.length >= 2) {
                        const mockOption = {
                            value: 1,
                            label: 'Mock Consultant Option',
                            data: { id: 1, name: 'Mock Consultant', email: 'consultant@example.com' }
                        };
                        onSelect(mockOption);
                    }
                }}
            />
        </div>
    ),
    SiteSelector: ({ onSelect, placeholder, ...props }: any) => (
        <div data-testid="site-selector">
            <input
                data-testid="site-input"
                placeholder={placeholder}
                onChange={(e) => {
                    if (e.target.value.length >= 2) {
                        const mockOption = {
                            value: 1,
                            label: 'Mock Site Option',
                            data: { id: 1, name: 'Mock Site', email: 'site@example.com' }
                        };
                        onSelect(mockOption);
                    }
                }}
            />
        </div>
    ),
    MultiPersonSelector: ({ onSelect, placeholder, ...props }: any) => (
        <div data-testid="multi-person-selector">
            <input
                data-testid="multi-person-input"
                placeholder={placeholder}
                onChange={(e) => {
                    if (e.target.value.length >= 2) {
                        const mockOptions = [
                            {
                                value: 1,
                                label: 'Mock Person 1',
                                data: { id: 1, name: 'Mock Person 1', email: 'person1@example.com' }
                            },
                            {
                                value: 2,
                                label: 'Mock Person 2',
                                data: { id: 2, name: 'Mock Person 2', email: 'person2@example.com' }
                            }
                        ];
                        onSelect(mockOptions);
                    }
                }}
            />
        </div>
    ),
    MultiConsultantSelector: ({ onSelect, placeholder, ...props }: any) => (
        <div data-testid="multi-consultant-selector">
            <input
                data-testid="multi-consultant-input"
                placeholder={placeholder}
                onChange={(e) => {
                    if (e.target.value.length >= 2) {
                        const mockOptions = [
                            {
                                value: 1,
                                label: 'Mock Consultant 1',
                                data: { id: 1, name: 'Mock Consultant 1', email: 'consultant1@example.com' }
                            }
                        ];
                        onSelect(mockOptions);
                    }
                }}
            />
        </div>
    ),
    MultiSiteSelector: ({ onSelect, placeholder, ...props }: any) => (
        <div data-testid="multi-site-selector">
            <input
                data-testid="multi-site-input"
                placeholder={placeholder}
                onChange={(e) => {
                    if (e.target.value.length >= 2) {
                        const mockOptions = [
                            {
                                value: 1,
                                label: 'Mock Site 1',
                                data: { id: 1, name: 'Mock Site 1', email: 'site1@example.com' }
                            }
                        ];
                        onSelect(mockOptions);
                    }
                }}
            />
        </div>
    ),
}));

describe('HybridSearchDemo', () => {
    beforeEach(() => {
        // Mock console.log to avoid noise in tests
        jest.spyOn(console, 'log').mockImplementation(() => {});
        jest.spyOn(console, 'error').mockImplementation(() => {});
    });

    afterEach(() => {
        jest.restoreAllMocks();
    });

    it('renders without crashing', () => {
        render(<HybridSearchDemo />);

        expect(screen.getByText('Hybrid Search System - React Demo')).toBeInTheDocument();
        expect(screen.getByText('Context7 uyumlu hibrit arama sistemi React Select implementasyonu')).toBeInTheDocument();
    });

    it('renders all form fields', () => {
        render(<HybridSearchDemo />);

        // Basic form fields
        expect(screen.getByLabelText('Başlık')).toBeInTheDocument();
        expect(screen.getByLabelText('Fiyat')).toBeInTheDocument();
        expect(screen.getByLabelText('Açıklama')).toBeInTheDocument();

        // Single select fields
        expect(screen.getByTestId('person-selector')).toBeInTheDocument();
        expect(screen.getByTestId('consultant-selector')).toBeInTheDocument();
        expect(screen.getByTestId('site-selector')).toBeInTheDocument();

        // Multi select fields
        expect(screen.getByTestId('multi-person-selector')).toBeInTheDocument();
        expect(screen.getByTestId('multi-consultant-selector')).toBeInTheDocument();
        expect(screen.getByTestId('multi-site-selector')).toBeInTheDocument();
    });

    it('handles form input changes', async () => {
        render(<HybridSearchDemo />);

        const titleInput = screen.getByLabelText('Başlık');
        const priceInput = screen.getByLabelText('Fiyat');
        const descriptionInput = screen.getByLabelText('Açıklama');

        await userEvent.type(titleInput, 'Test Title');
        await userEvent.type(priceInput, '100000');
        await userEvent.type(descriptionInput, 'Test Description');

        expect(titleInput).toHaveValue('Test Title');
        expect(priceInput).toHaveValue(100000);
        expect(descriptionInput).toHaveValue('Test Description');
    });

    it('handles single person selection', async () => {
        render(<HybridSearchDemo />);

        const personInput = screen.getByTestId('person-input');
        await userEvent.type(personInput, 'test');

        await waitFor(() => {
            expect(screen.getByText('Seçilen: Mock Person Option')).toBeInTheDocument();
        });
    });

    it('handles single consultant selection', async () => {
        render(<HybridSearchDemo />);

        const consultantInput = screen.getByTestId('consultant-input');
        await userEvent.type(consultantInput, 'test');

        await waitFor(() => {
            expect(screen.getByText('Seçilen: Mock Consultant Option')).toBeInTheDocument();
        });
    });

    it('handles single site selection', async () => {
        render(<HybridSearchDemo />);

        const siteInput = screen.getByTestId('site-input');
        await userEvent.type(siteInput, 'test');

        await waitFor(() => {
            expect(screen.getByText('Seçilen: Mock Site Option')).toBeInTheDocument();
        });
    });

    it('handles multi person selection', async () => {
        render(<HybridSearchDemo />);

        const multiPersonInput = screen.getByTestId('multi-person-input');
        await userEvent.type(multiPersonInput, 'test');

        await waitFor(() => {
            expect(screen.getByText('Seçilen (2):')).toBeInTheDocument();
            expect(screen.getByText('• Mock Person 1')).toBeInTheDocument();
            expect(screen.getByText('• Mock Person 2')).toBeInTheDocument();
        });
    });

    it('handles multi consultant selection', async () => {
        render(<HybridSearchDemo />);

        const multiConsultantInput = screen.getByTestId('multi-consultant-input');
        await userEvent.type(multiConsultantInput, 'test');

        await waitFor(() => {
            expect(screen.getByText('Seçilen (1):')).toBeInTheDocument();
            expect(screen.getByText('• Mock Consultant 1')).toBeInTheDocument();
        });
    });

    it('handles multi site selection', async () => {
        render(<HybridSearchDemo />);

        const multiSiteInput = screen.getByTestId('multi-site-input');
        await userEvent.type(multiSiteInput, 'test');

        await waitFor(() => {
            expect(screen.getByText('Seçilen (1):')).toBeInTheDocument();
            expect(screen.getByText('• Mock Site 1')).toBeInTheDocument();
        });
    });

    it('handles form submission', async () => {
        // Mock alert
        window.alert = jest.fn();

        render(<HybridSearchDemo />);

        // Fill form
        const titleInput = screen.getByLabelText('Başlık');
        const priceInput = screen.getByLabelText('Fiyat');
        const descriptionInput = screen.getByLabelText('Açıklama');

        await userEvent.type(titleInput, 'Test Title');
        await userEvent.type(priceInput, '100000');
        await userEvent.type(descriptionInput, 'Test Description');

        // Select options
        const personInput = screen.getByTestId('person-input');
        await userEvent.type(personInput, 'test');

        await waitFor(() => {
            expect(screen.getByText('Seçilen: Mock Person Option')).toBeInTheDocument();
        });

        // Submit form
        const submitButton = screen.getByText('Formu Gönder');
        fireEvent.click(submitButton);

        expect(window.alert).toHaveBeenCalledWith('Form submitted! Check console for data.');
    });

    it('handles clear all functionality', async () => {
        render(<HybridSearchDemo />);

        // Fill form
        const titleInput = screen.getByLabelText('Başlık');
        const priceInput = screen.getByLabelText('Fiyat');

        await userEvent.type(titleInput, 'Test Title');
        await userEvent.type(priceInput, '100000');

        // Select options
        const personInput = screen.getByTestId('person-input');
        await userEvent.type(personInput, 'test');

        await waitFor(() => {
            expect(screen.getByText('Seçilen: Mock Person Option')).toBeInTheDocument();
        });

        // Clear all
        const clearButton = screen.getByText('Temizle');
        fireEvent.click(clearButton);

        // Check if form is cleared
        expect(titleInput).toHaveValue('');
        expect(priceInput).toHaveValue(null);
        expect(screen.queryByText('Seçilen: Mock Person Option')).not.toBeInTheDocument();
    });

    it('displays selection summary', async () => {
        render(<HybridSearchDemo />);

        // Initially should show "Seçilmedi"
        expect(screen.getByText('Kişi: Seçilmedi')).toBeInTheDocument();
        expect(screen.getByText('Danışman: Seçilmedi')).toBeInTheDocument();
        expect(screen.getByText('Site: Seçilmedi')).toBeInTheDocument();
        expect(screen.getByText('Çoklu Kişiler: 0 seçim')).toBeInTheDocument();

        // Select options
        const personInput = screen.getByTestId('person-input');
        await userEvent.type(personInput, 'test');

        await waitFor(() => {
            expect(screen.getByText('Kişi: Mock Person Option')).toBeInTheDocument();
        });
    });

    it('renders advanced configuration section', () => {
        render(<HybridSearchDemo />);

        expect(screen.getByText('Gelişmiş Konfigürasyon')).toBeInTheDocument();
        expect(screen.getByText('Özelleştirilmiş Kişi Seçimi')).toBeInTheDocument();
    });

    it('has proper accessibility attributes', () => {
        render(<HybridSearchDemo />);

        // Check for proper labels
        expect(screen.getByLabelText('Başlık')).toBeInTheDocument();
        expect(screen.getByLabelText('Fiyat')).toBeInTheDocument();
        expect(screen.getByLabelText('Açıklama')).toBeInTheDocument();

        // Check for proper button text
        expect(screen.getByRole('button', { name: 'Temizle' })).toBeInTheDocument();
        expect(screen.getByRole('button', { name: 'Formu Gönder' })).toBeInTheDocument();
    });

    it('handles keyboard navigation', async () => {
        render(<HybridSearchDemo />);

        const titleInput = screen.getByLabelText('Başlık');
        const priceInput = screen.getByLabelText('Fiyat');

        // Tab navigation
        titleInput.focus();
        expect(titleInput).toHaveFocus();

        await userEvent.tab();
        expect(priceInput).toHaveFocus();
    });

    it('renders all sections with proper headings', () => {
        render(<HybridSearchDemo />);

        expect(screen.getByText('Tekli Seçim Örnekleri')).toBeInTheDocument();
        expect(screen.getByText('Çoklu Seçim Örnekleri')).toBeInTheDocument();
        expect(screen.getByText('Gelişmiş Konfigürasyon')).toBeInTheDocument();
        expect(screen.getByText('Seçim Özeti')).toBeInTheDocument();
    });
});
