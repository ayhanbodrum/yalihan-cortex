import React, { useState, useEffect } from 'react';
import { Property, PropertyImage, PropertyFeature } from '../types/property';

interface PropertyViewerProps {
  property: Property;
  onClose?: () => void;
  showMap?: boolean;
  showVirtualTour?: boolean;
  showGallery?: boolean;
  showShare?: boolean;
}

const PropertyViewer: React.FC<PropertyViewerProps> = ({
  property,
  onClose,
  showMap = true,
  showVirtualTour = true,
  showGallery = true,
  showShare = true
}) => {
  const [activeImageIndex, setActiveImageIndex] = useState(0);
  const [isLoading, setIsLoading] = useState(false);
  const [showFullscreen, setShowFullscreen] = useState(false);

  // Context7 Live Search integration for related properties
  const [relatedProperties, setRelatedProperties] = useState<Property[]>([]);

  useEffect(() => {
    // Load related properties using Context7 Live Search
    loadRelatedProperties();
  }, [property.id]);

  const loadRelatedProperties = async () => {
    setIsLoading(true);
    try {
      const response = await fetch(`/api/hybrid-search/properties?related_to=${property.id}`);
      const data = await response.json();
      if (data.success) {
        setRelatedProperties(data.data);
      }
    } catch (error) {
      console.error('Related properties loading error:', error);
    } finally {
      setIsLoading(false);
    }
  };

  const formatPrice = (price: number, currency: string = '₺') => {
    return new Intl.NumberFormat('tr-TR').format(price) + ' ' + currency;
  };

  const handleImageNavigation = (direction: 'prev' | 'next') => {
    if (!property.images || property.images.length === 0) return;

    if (direction === 'prev') {
      setActiveImageIndex(prev =>
        prev === 0 ? property.images!.length - 1 : prev - 1
      );
    } else {
      setActiveImageIndex(prev =>
        prev === property.images!.length - 1 ? 0 : prev + 1
      );
    }
  };

  const handleShare = async () => {
    if (navigator.share) {
      try {
        await navigator.share({
          title: property.title,
          text: property.description,
          url: window.location.href
        });
      } catch (error) {
        console.log('Share cancelled');
      }
    } else {
      // Fallback: Copy to clipboard
      await navigator.clipboard.writeText(window.location.href);
      // Show toast notification
    }
  };

  return (
    <div className="property-viewer bg-white rounded-2xl shadow-xl overflow-hidden">
      {/* Header */}
      <div className="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold">{property.title}</h1>
            <p className="text-blue-100 mt-1">{property.location}</p>
          </div>
          <div className="flex items-center space-x-3">
            {showShare && (
              <button
                onClick={handleShare}
                className="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors"
                title="Paylaş"
              >
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                </svg>
              </button>
            )}
            {onClose && (
              <button
                onClick={onClose}
                className="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors"
                title="Kapat"
              >
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            )}
          </div>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-0">
        {/* Main Content */}
        <div className="lg:col-span-2 p-6">
          {/* Image Gallery */}
          {showGallery && property.images && property.images.length > 0 && (
            <div className="mb-8">
              <div className="relative">
                <div className="aspect-video bg-gray-100 rounded-xl overflow-hidden">
                  <img
                    src={property.images[activeImageIndex]?.url}
                    alt={property.title}
                    className="w-full h-full object-cover"
                  />
                </div>

                {/* Navigation Arrows */}
                {property.images.length > 1 && (
                  <>
                    <button
                      onClick={() => handleImageNavigation('prev')}
                      className="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black/50 text-white p-2 rounded-full hover:bg-black/70 transition-colors"
                    >
                      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                      </svg>
                    </button>
                    <button
                      onClick={() => handleImageNavigation('next')}
                      className="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black/50 text-white p-2 rounded-full hover:bg-black/70 transition-colors"
                    >
                      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                      </svg>
                    </button>
                  </>
                )}

                {/* Image Counter */}
                <div className="absolute bottom-4 right-4 bg-black/50 text-white px-3 py-1 rounded-full text-sm">
                  {activeImageIndex + 1} / {property.images.length}
                </div>
              </div>

              {/* Thumbnail Strip */}
              {property.images.length > 1 && (
                <div className="flex space-x-2 mt-4 overflow-x-auto">
                  {property.images.map((image, index) => (
                    <button
                      key={index}
                      onClick={() => setActiveImageIndex(index)}
                      className={`flex-shrink-0 w-20 h-16 rounded-lg overflow-hidden border-2 transition-colors ${
                        activeImageIndex === index ? 'border-blue-500' : 'border-gray-200'
                      }`}
                    >
                      <img
                        src={image.url}
                        alt={`${property.title} ${index + 1}`}
                        className="w-full h-full object-cover"
                      />
                    </button>
                  ))}
                </div>
              )}
            </div>
          )}

          {/* Property Details */}
          <div className="space-y-6">
            {/* Price */}
            <div className="text-center p-6 bg-gradient-to-r from-green-50 to-blue-50 rounded-xl">
              <div className="text-4xl font-bold text-gray-900">
                {formatPrice(property.price, property.currency)}
              </div>
              <div className="text-lg text-gray-600 mt-1">{property.type}</div>
            </div>

            {/* Features */}
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              {property.features?.map((feature, index) => (
                <div key={index} className="text-center p-4 bg-gray-50 rounded-lg">
                  <div className="text-2xl font-bold text-blue-600">{feature.value}</div>
                  <div className="text-sm text-gray-600">{feature.label}</div>
                </div>
              ))}
            </div>

            {/* Description */}
            <div>
              <h3 className="text-xl font-semibold mb-4">Açıklama</h3>
              <p className="text-gray-700 leading-relaxed">{property.description}</p>
            </div>

            {/* Virtual Tour */}
            {showVirtualTour && property.virtualTourUrl && (
              <div>
                <h3 className="text-xl font-semibold mb-4">360° Sanal Tur</h3>
                <div className="aspect-video bg-gray-100 rounded-xl overflow-hidden">
                  <iframe
                    src={property.virtualTourUrl}
                    className="w-full h-full"
                    allowFullScreen
                    title="360° Sanal Tur"
                  />
                </div>
              </div>
            )}
          </div>
        </div>

        {/* Sidebar */}
        <div className="bg-gray-50 p-6 space-y-6">
          {/* Agent Info */}
          {property.agent && (
            <div className="bg-white rounded-xl p-6 shadow-sm">
              <h3 className="text-lg font-semibold mb-4">Danışman Bilgileri</h3>
              <div className="flex items-center space-x-4">
                <img
                  src={property.agent.avatar}
                  alt={property.agent.name}
                  className="w-16 h-16 rounded-full object-cover"
                />
                <div>
                  <div className="font-semibold">{property.agent.name}</div>
                  <div className="text-sm text-gray-600">{property.agent.phone}</div>
                  <div className="flex items-center mt-1">
                    <div className="flex text-yellow-400">
                      {[...Array(5)].map((_, i) => (
                        <svg
                          key={i}
                          className={`w-4 h-4 ${i < Math.floor(property.agent.rating) ? 'text-yellow-400' : 'text-gray-300'}`}
                          fill="currentColor"
                          viewBox="0 0 20 20"
                        >
                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                      ))}
                    </div>
                    <span className="text-sm text-gray-600 ml-2">({property.agent.rating})</span>
                  </div>
                </div>
              </div>
              <div className="mt-4 space-y-2">
                <a
                  href={`tel:${property.agent.phone}`}
                  className="block w-full bg-blue-600 text-white text-center py-2 rounded-lg hover:bg-blue-700 transition-colors"
                >
                  📞 Ara
                </a>
                <a
                  href={`https://wa.me/${property.agent.phone.replace(/[^0-9]/g, '')}`}
                  className="block w-full bg-green-600 text-white text-center py-2 rounded-lg hover:bg-green-700 transition-colors"
                >
                  💬 WhatsApp
                </a>
              </div>
            </div>
          )}

          {/* Map */}
          {showMap && property.coordinates && (
            <div className="bg-white rounded-xl p-6 shadow-sm">
              <h3 className="text-lg font-semibold mb-4">Konum</h3>
              <div className="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                <iframe
                  src={`https://www.google.com/maps/embed/v1/place?key=YOUR_API_KEY&q=${property.coordinates.lat},${property.coordinates.lng}`}
                  className="w-full h-full"
                  allowFullScreen
                  title="Property Location"
                />
              </div>
            </div>
          )}

          {/* Related Properties */}
          {relatedProperties.length > 0 && (
            <div className="bg-white rounded-xl p-6 shadow-sm">
              <h3 className="text-lg font-semibold mb-4">Benzer İlanlar</h3>
              <div className="space-y-4">
                {relatedProperties.slice(0, 3).map((relatedProperty) => (
                  <div key={relatedProperty.id} className="flex space-x-3">
                    <img
                      src={relatedProperty.images?.[0]?.url || '/images/placeholder.jpg'}
                      alt={relatedProperty.title}
                      className="w-16 h-16 rounded-lg object-cover"
                    />
                    <div className="flex-1">
                      <div className="font-medium text-sm">{relatedProperty.title}</div>
                      <div className="text-sm text-gray-600">{relatedProperty.location}</div>
                      <div className="text-sm font-semibold text-blue-600">
                        {formatPrice(relatedProperty.price, relatedProperty.currency)}
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default PropertyViewer;












