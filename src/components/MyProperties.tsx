import React, { useState, useEffect } from 'react';
import { Property, PropertyFilter } from '../types/property';

interface MyPropertiesProps {
  initialProperties?: Property[];
  onPropertySelect?: (property: Property) => void;
  onPropertyEdit?: (property: Property) => void;
  onPropertyDelete?: (property: Property) => void;
}

const MyProperties: React.FC<MyPropertiesProps> = ({
  initialProperties = [],
  onPropertySelect,
  onPropertyEdit,
  onPropertyDelete
}) => {
  const [properties, setProperties] = useState<Property[]>(initialProperties);
  const [filteredProperties, setFilteredProperties] = useState<Property[]>(initialProperties);
  const [isLoading, setIsLoading] = useState(false);
  const [searchTerm, setSearchTerm] = useState('');
  const [filters, setFilters] = useState<PropertyFilter>({});
  const [selectedProperties, setSelectedProperties] = useState<number[]>([]);
  const [showBulkActions, setShowBulkActions] = useState(false);

  // Context7 Live Search integration
  const [searchResults, setSearchResults] = useState<Property[]>([]);
  const [isSearching, setIsSearching] = useState(false);

  useEffect(() => {
    loadProperties();
  }, []);

  useEffect(() => {
    applyFilters();
  }, [properties, filters, searchTerm]);

  const loadProperties = async () => {
    setIsLoading(true);
    try {
      const response = await fetch('/api/my-properties');
      const data = await response.json();
      if (data.success) {
        setProperties(data.data);
      }
    } catch (error) {
      console.error('Properties loading error:', error);
    } finally {
      setIsLoading(false);
    }
  };

  const applyFilters = () => {
    let filtered = [...properties];

    // Search term filter
    if (searchTerm) {
      filtered = filtered.filter(property =>
        property.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
        property.location.toLowerCase().includes(searchTerm.toLowerCase()) ||
        property.address.toLowerCase().includes(searchTerm.toLowerCase())
      );
    }

    // Status filter
    if (filters.status) {
      filtered = filtered.filter(property => property.status === filters.status);
    }

    // Type filter
    if (filters.type) {
      filtered = filtered.filter(property => property.type === filters.type);
    }

    // Price range filter
    if (filters.minPrice) {
      filtered = filtered.filter(property => property.price >= filters.minPrice!);
    }
    if (filters.maxPrice) {
      filtered = filtered.filter(property => property.price <= filters.maxPrice!);
    }

    setFilteredProperties(filtered);
  };

  const handleSearch = async (term: string) => {
    if (term.length < 2) {
      setSearchResults([]);
      return;
    }

    setIsSearching(true);
    try {
      const response = await fetch(`/api/hybrid-search/properties?q=${encodeURIComponent(term)}`);
      const data = await response.json();
      if (data.success) {
        setSearchResults(data.data);
      }
    } catch (error) {
      console.error('Search error:', error);
    } finally {
      setIsSearching(false);
    }
  };

  const handlePropertySelect = (property: Property) => {
    setSelectedProperties(prev =>
      prev.includes(property.id)
        ? prev.filter(id => id !== property.id)
        : [...prev, property.id]
    );
  };

  const handleBulkAction = async (action: string) => {
    if (selectedProperties.length === 0) return;

    setIsLoading(true);
    try {
      const response = await fetch('/api/properties/bulk-action', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          propertyIds: selectedProperties,
          action: action
        })
      });

      const data = await response.json();
      if (data.success) {
        // Refresh properties
        await loadProperties();
        setSelectedProperties([]);
        setShowBulkActions(false);
      }
    } catch (error) {
      console.error('Bulk action error:', error);
    } finally {
      setIsLoading(false);
    }
  };

  const formatPrice = (price: number, currency: string = '₺') => {
    return new Intl.NumberFormat('tr-TR').format(price) + ' ' + currency;
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'active': return 'bg-green-100 text-green-800';
      case 'pending': return 'bg-yellow-100 text-yellow-800';
      case 'inactive': return 'bg-red-100 text-red-800';
      case 'draft': return 'bg-gray-100 text-gray-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const getStatusText = (status: string) => {
    switch (status) {
      case 'active': return 'Aktif';
      case 'pending': return 'Beklemede';
      case 'inactive': return 'Pasif';
      case 'draft': return 'Taslak';
      default: return status;
    }
  };

  return (
    <div className="my-properties bg-white rounded-2xl shadow-lg">
      {/* Header */}
      <div className="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-6 rounded-t-2xl">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold">🏠 İlanlarım</h1>
            <p className="text-blue-100 mt-1">
              {filteredProperties.length} ilan • {properties.filter(p => p.status === 'active').length} aktif
            </p>
          </div>
          <div className="flex items-center space-x-3">
            <button
              onClick={loadProperties}
              className="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors"
              title="Yenile"
            >
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.001 0 01-15.357-2m15.357 2H15" />
              </svg>
            </button>
            <a
              href="/admin/ilanlar/create"
              className="px-4 py-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors font-medium"
            >
              ➕ Yeni İlan
            </a>
          </div>
        </div>
      </div>

      {/* Filters */}
      <div className="p-6 border-b border-gray-200">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
          {/* Search */}
          <div className="md:col-span-2">
            <label className="block text-sm font-medium text-gray-700 mb-2">
              🔍 Arama
            </label>
            <input
              type="text"
              value={searchTerm}
              onChange={(e) => {
                setSearchTerm(e.target.value);
                handleSearch(e.target.value);
              }}
              placeholder="İlan ara..."
              className="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
            />
          </div>

          {/* Status Filter */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              📊 Durum
            </label>
            <select
              value={filters.status || ''}
              onChange={(e) => setFilters(prev => ({ ...prev, status: e.target.value || undefined }))}
              className="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
            >
              <option value="">Tümü</option>
              <option value="active">Aktif</option>
              <option value="pending">Beklemede</option>
              <option value="inactive">Pasif</option>
              <option value="draft">Taslak</option>
            </select>
          </div>

          {/* Type Filter */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              🏷️ Tür
            </label>
            <select
              value={filters.type || ''}
              onChange={(e) => setFilters(prev => ({ ...prev, type: e.target.value || undefined }))}
              className="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
            >
              <option value="">Tümü</option>
              <option value="Satılık">Satılık</option>
              <option value="Kiralık">Kiralık</option>
            </select>
          </div>
        </div>

        {/* Bulk Actions */}
        {selectedProperties.length > 0 && (
          <div className="mt-4 p-4 bg-blue-50 rounded-lg">
            <div className="flex items-center justify-between">
              <span className="text-blue-800 font-medium">
                {selectedProperties.length} ilan seçildi
              </span>
              <div className="flex space-x-2">
                <button
                  onClick={() => handleBulkAction('activate')}
                  className="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition-colors"
                >
                  Aktifleştir
                </button>
                <button
                  onClick={() => handleBulkAction('deactivate')}
                  className="px-3 py-1 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition-colors"
                >
                  Pasifleştir
                </button>
                <button
                  onClick={() => handleBulkAction('delete')}
                  className="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-colors"
                >
                  Sil
                </button>
                <button
                  onClick={() => setSelectedProperties([])}
                  className="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors"
                >
                  İptal
                </button>
              </div>
            </div>
          </div>
        )}
      </div>

      {/* Properties Grid */}
      <div className="p-6">
        {isLoading ? (
          <div className="text-center py-12">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
            <p className="mt-4 text-gray-600">İlanlar yükleniyor...</p>
          </div>
        ) : filteredProperties.length === 0 ? (
          <div className="text-center py-12">
            <div className="text-6xl mb-4">🏠</div>
            <h3 className="text-xl font-semibold text-gray-900 mb-2">İlan bulunamadı</h3>
            <p className="text-gray-600 mb-4">
              {searchTerm ? 'Arama kriterlerinize uygun ilan bulunamadı.' : 'Henüz ilan eklenmemiş.'}
            </p>
            <a
              href="/admin/ilanlar/create"
              className="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              ➕ İlk İlanını Ekle
            </a>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredProperties.map((property) => (
              <div
                key={property.id}
                className={`bg-white border-2 rounded-xl overflow-hidden hover:shadow-lg transition-all duration-200 ${
                  selectedProperties.includes(property.id) ? 'border-blue-500 bg-blue-50' : 'border-gray-200'
                }`}
              >
                {/* Checkbox */}
                <div className="p-3 bg-gray-50">
                  <input
                    type="checkbox"
                    checked={selectedProperties.includes(property.id)}
                    onChange={() => handlePropertySelect(property)}
                    className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  />
                </div>

                {/* Image */}
                <div className="relative h-48 bg-gray-100">
                  <img
                    src={property.images?.[0]?.url || '/images/placeholder-property.jpg'}
                    alt={property.title}
                    className="w-full h-full object-cover"
                  />
                  <div className="absolute top-3 left-3">
                    <span className={`px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(property.status)}`}>
                      {getStatusText(property.status)}
                    </span>
                  </div>
                  <div className="absolute top-3 right-3">
                    <span className="bg-black/50 text-white px-2 py-1 rounded text-xs">
                      {property.type}
                    </span>
                  </div>
                </div>

                {/* Content */}
                <div className="p-4">
                  <h3 className="font-semibold text-lg mb-2 line-clamp-2">{property.title}</h3>
                  <p className="text-gray-600 text-sm mb-3">{property.location}</p>

                  <div className="text-2xl font-bold text-blue-600 mb-4">
                    {formatPrice(property.price, property.currency)}
                  </div>

                  {/* Stats */}
                  <div className="flex justify-between text-sm text-gray-500 mb-4">
                    <span>👁️ {property.viewCount}</span>
                    <span>❤️ {property.favoriteCount}</span>
                    <span>📞 {property.contactCount}</span>
                  </div>

                  {/* Actions */}
                  <div className="flex space-x-2">
                    <button
                      onClick={() => onPropertySelect?.(property)}
                      className="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm"
                    >
                      👁️ Görüntüle
                    </button>
                    <button
                      onClick={() => onPropertyEdit?.(property)}
                      className="flex-1 bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-700 transition-colors text-sm"
                    >
                      ✏️ Düzenle
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
};

export default MyProperties;
