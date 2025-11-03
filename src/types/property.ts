export interface PropertyImage {
  id: number;
  url: string;
  alt: string;
  isPrimary: boolean;
  order: number;
}

export interface PropertyFeature {
  label: string;
  value: string | number;
  icon?: string;
}

export interface PropertyAgent {
  id: number;
  name: string;
  phone: string;
  email: string;
  avatar: string;
  rating: number;
  propertiesCount: number;
  experience: number;
}

export interface PropertyCoordinates {
  lat: number;
  lng: number;
}

export interface Property {
  id: number;
  title: string;
  description: string;
  price: number;
  currency: string;
  type: string; // 'Satılık', 'Kiralık'
  location: string;
  address: string;
  images?: PropertyImage[];
  features?: PropertyFeature[];
  agent?: PropertyAgent;
  coordinates?: PropertyCoordinates;
  virtualTourUrl?: string;
  droneImages?: PropertyImage[];
  neighborhoodAnalysis?: {
    schools: Array<{
      name: string;
      distance: number;
      rating: number;
    }>;
    hospitals: Array<{
      name: string;
      distance: number;
      type: string;
    }>;
    shopping: Array<{
      name: string;
      distance: number;
      type: string;
    }>;
    transport: Array<{
      name: string;
      distance: number;
      type: string;
    }>;
  };
  investmentAnalysis?: {
    locationScore: number;
    infrastructureScore: number;
    marketTrendScore: number;
    accessibilityScore: number;
    totalScore: number;
    confidenceLevel: number;
    predictionDate: string;
  };
  createdAt: string;
  updatedAt: string;
  status: 'active' | 'pending' | 'inactive' | 'draft';
  viewCount: number;
  favoriteCount: number;
  contactCount: number;
}

export interface PropertyFilter {
  minPrice?: number;
  maxPrice?: number;
  type?: string;
  location?: string;
  features?: string[];
  status?: string;
}

export interface PropertySearchResult {
  properties: Property[];
  total: number;
  page: number;
  perPage: number;
  totalPages: number;
  filters: PropertyFilter;
}













