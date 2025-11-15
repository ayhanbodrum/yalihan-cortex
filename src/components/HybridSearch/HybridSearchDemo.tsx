/**
 * Hybrid Search System - React Demo Component
 *
 * Context7 Standardı: C7-HYBRID-SEARCH-DEMO-2025-01-30
 * Versiyon: 1.0.0
 * Son Güncelleme: 30 Ocak 2025
 * Durum: ✅ Production Ready
 */

import React, { useState, useCallback } from 'react';
import {
    HybridSearchReactSelect,
    PersonSelector,
    ConsultantSelector,
    SiteSelector,
    MultiPersonSelector,
    MultiConsultantSelector,
    MultiSiteSelector,
} from './ReactSelectSearch';
import { HybridSearchOption } from '../../types/HybridSearch';

// Demo form component
const HybridSearchDemo: React.FC = () => {
    const [selectedPerson, setSelectedPerson] = useState<HybridSearchOption | null>(null);
    const [selectedConsultant, setSelectedConsultant] = useState<HybridSearchOption | null>(null);
    const [selectedSite, setSelectedSite] = useState<HybridSearchOption | null>(null);
    const [selectedMultiPersons, setSelectedMultiPersons] = useState<HybridSearchOption[]>([]);
    const [selectedMultiConsultants, setSelectedMultiConsultants] = useState<HybridSearchOption[]>([]);
    const [selectedMultiSites, setSelectedMultiSites] = useState<HybridSearchOption[]>([]);
    const [formData, setFormData] = useState({
        title: '',
        description: '',
        price: '',
    });

    // Handle single selections
    const handlePersonSelect = useCallback((option: HybridSearchOption | null) => {
        setSelectedPerson(option);
        console.log('Selected person:', option);
    }, []);

    const handleConsultantSelect = useCallback((option: HybridSearchOption | null) => {
        setSelectedConsultant(option);
        console.log('Selected consultant:', option);
    }, []);

    const handleSiteSelect = useCallback((option: HybridSearchOption | null) => {
        setSelectedSite(option);
        console.log('Selected site:', option);
    }, []);

    // Handle multi selections
    const handleMultiPersonSelect = useCallback((options: HybridSearchOption[] | null) => {
        setSelectedMultiPersons(options || []);
        console.log('Selected persons:', options);
    }, []);

    const handleMultiConsultantSelect = useCallback((options: HybridSearchOption[] | null) => {
        setSelectedMultiConsultants(options || []);
        console.log('Selected consultants:', options);
    }, []);

    const handleMultiSiteSelect = useCallback((options: HybridSearchOption[] | null) => {
        setSelectedMultiSites(options || []);
        console.log('Selected sites:', options);
    }, []);

    // Handle form submission
    const handleSubmit = useCallback((e: React.FormEvent) => {
        e.preventDefault();

        const data = {
            ...formData,
            person: selectedPerson,
            consultant: selectedConsultant,
            site: selectedSite,
            multiPersons: selectedMultiPersons,
            multiConsultants: selectedMultiConsultants,
            multiSites: selectedMultiSites,
        };

        console.log('Form submitted:', data);
        alert('Form submitted! Check console for data.');
    }, [formData, selectedPerson, selectedConsultant, selectedSite, selectedMultiPersons, selectedMultiConsultants, selectedMultiSites]);

    // Handle form input changes
    const handleInputChange = useCallback((e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value,
        }));
    }, []);

    // Clear all selections
    const handleClearAll = useCallback(() => {
        setSelectedPerson(null);
        setSelectedConsultant(null);
        setSelectedSite(null);
        setSelectedMultiPersons([]);
        setSelectedMultiConsultants([]);
        setSelectedMultiSites([]);
        setFormData({
            title: '',
            description: '',
            price: '',
        });
    }, []);

    return (
        <div className="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-lg">
            <div className="mb-8">
                <h1 className="text-3xl font-bold text-gray-900 mb-2">
                    Hybrid Search System - React Demo
                </h1>
                <p className="text-gray-600">
                    Context7 uyumlu hibrit arama sistemi React Select implementasyonu
                </p>
            </div>

            <form onSubmit={handleSubmit} className="space-y-6">
                {/* Basic Form Fields */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label htmlFor="title" className="block text-sm font-medium text-gray-700 mb-1">
                            Başlık
                        </label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            value={formData.title}
                            onChange={handleInputChange}
                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Başlık girin..."
                        />
                    </div>
                    <div>
                        <label htmlFor="price" className="block text-sm font-medium text-gray-700 mb-1">
                            Fiyat
                        </label>
                        <input
                            type="number"
                            id="price"
                            name="price"
                            value={formData.price}
                            onChange={handleInputChange}
                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Fiyat girin..."
                        />
                    </div>
                </div>

                <div>
                    <label htmlFor="description" className="block text-sm font-medium text-gray-700 mb-1">
                        Açıklama
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        value={formData.description}
                        onChange={handleInputChange}
                        rows={4}
                        className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Açıklama girin..."
                    />
                </div>

                {/* Single Select Examples */}
                <div className="border-t pt-6">
                    <h2 className="text-xl font-semibold text-gray-900 mb-4">Tekli Seçim Örnekleri</h2>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Kişi Seçimi
                            </label>
                            <PersonSelector
                                onSelect={handlePersonSelect}
                                placeholder="Kişi ara ve seç..."
                                isClearable={true}
                            />
                            {selectedPerson && (
                                <div className="mt-2 p-2 bg-green-50 border border-green-200 rounded text-sm">
                                    <strong>Seçilen:</strong> {selectedPerson.label}
                                </div>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Danışman Seçimi
                            </label>
                            <ConsultantSelector
                                onSelect={handleConsultantSelect}
                                placeholder="Danışman ara ve seç..."
                                isClearable={true}
                            />
                            {selectedConsultant && (
                                <div className="mt-2 p-2 bg-green-50 border border-green-200 rounded text-sm">
                                    <strong>Seçilen:</strong> {selectedConsultant.label}
                                </div>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Site/Apartman Seçimi
                            </label>
                            <SiteSelector
                                onSelect={handleSiteSelect}
                                placeholder="Site ara ve seç..."
                                isClearable={true}
                            />
                            {selectedSite && (
                                <div className="mt-2 p-2 bg-green-50 border border-green-200 rounded text-sm">
                                    <strong>Seçilen:</strong> {selectedSite.label}
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Multi Select Examples */}
                <div className="border-t pt-6">
                    <h2 className="text-xl font-semibold text-gray-900 mb-4">Çoklu Seçim Örnekleri</h2>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Çoklu Kişi Seçimi
                            </label>
                            <MultiPersonSelector
                                onSelect={handleMultiPersonSelect}
                                placeholder="Birden fazla kişi seç..."
                                isClearable={true}
                            />
                            {selectedMultiPersons.length > 0 && (
                                <div className="mt-2 p-2 bg-green-50 border border-green-200 rounded text-sm">
                                    <strong>Seçilen ({selectedMultiPersons.length}):</strong>
                                    <ul className="mt-1 space-y-1">
                                        {selectedMultiPersons.map((person) => (
                                            <li key={person.value} className="text-xs">
                                                • {person.label}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Çoklu Danışman Seçimi
                            </label>
                            <MultiConsultantSelector
                                onSelect={handleMultiConsultantSelect}
                                placeholder="Birden fazla danışman seç..."
                                isClearable={true}
                            />
                            {selectedMultiConsultants.length > 0 && (
                                <div className="mt-2 p-2 bg-green-50 border border-green-200 rounded text-sm">
                                    <strong>Seçilen ({selectedMultiConsultants.length}):</strong>
                                    <ul className="mt-1 space-y-1">
                                        {selectedMultiConsultants.map((consultant) => (
                                            <li key={consultant.value} className="text-xs">
                                                • {consultant.label}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Çoklu Site Seçimi
                            </label>
                            <MultiSiteSelector
                                onSelect={handleMultiSiteSelect}
                                placeholder="Birden fazla site seç..."
                                isClearable={true}
                            />
                            {selectedMultiSites.length > 0 && (
                                <div className="mt-2 p-2 bg-green-50 border border-green-200 rounded text-sm">
                                    <strong>Seçilen ({selectedMultiSites.length}):</strong>
                                    <ul className="mt-1 space-y-1">
                                        {selectedMultiSites.map((site) => (
                                            <li key={site.value} className="text-xs">
                                                • {site.label}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                {/* Advanced Configuration Example */}
                <div className="border-t pt-6">
                    <h2 className="text-xl font-semibold text-gray-900 mb-4">Gelişmiş Konfigürasyon</h2>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">
                            Özelleştirilmiş Kişi Seçimi
                        </label>
                        <HybridSearchReactSelect
                            searchType="kisiler"
                            onSelect={handlePersonSelect}
                            placeholder="Gelişmiş kişi arama..."
                            isClearable={true}
                            maxResults={10}
                            debounceMs={500}
                            className="custom-search-input"
                            loadingMessage="Kişiler aranıyor..."
                            noOptionsMessage="Kişi bulunamadı"
                            errorMessage="Arama sırasında hata oluştu"
                        />
                    </div>
                </div>

                {/* Form Actions */}
                <div className="border-t pt-6 flex justify-between">
                    <button
                        type="button"
                        onClick={handleClearAll}
                        className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Temizle
                    </button>

                    <button
                        type="submit"
                        className="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Formu Gönder
                    </button>
                </div>
            </form>

            {/* Summary */}
            <div className="mt-8 p-4 bg-gray-50 rounded-lg">
                <h3 className="text-lg font-medium text-gray-900 mb-2">Seçim Özeti</h3>
                <div className="space-y-2 text-sm text-gray-600">
                    <div><strong>Kişi:</strong> {selectedPerson ? selectedPerson.label : 'Seçilmedi'}</div>
                    <div><strong>Danışman:</strong> {selectedConsultant ? selectedConsultant.label : 'Seçilmedi'}</div>
                    <div><strong>Site:</strong> {selectedSite ? selectedSite.label : 'Seçilmedi'}</div>
                    <div><strong>Çoklu Kişiler:</strong> {selectedMultiPersons.length} seçim</div>
                    <div><strong>Çoklu Danışmanlar:</strong> {selectedMultiConsultants.length} seçim</div>
                    <div><strong>Çoklu Siteler:</strong> {selectedMultiSites.length} seçim</div>
                </div>
            </div>
        </div>
    );
};

export default HybridSearchDemo;
