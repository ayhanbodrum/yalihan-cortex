@extends('layouts.frontend')

@section('title', 'İlan Detayı - Yalıhan Emlak')

@section('content')
    <x-yaliihan.property-detail title="Modern Villa - Yalıkavak" location="Yalıkavak, Bodrum" price="₺8,500,000"
        :beds="4" :baths="3" :area="250" badge="sale" badge-text="Satılık" :images="[
            'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&h=600&fit=crop',
            'https://images.unsplash.com/photo-1600607687644-c7171b42498b?w=800&h=600&fit=crop',
        ]"
        description="Bu harika villa, Bodrum'un en prestijli bölgelerinden biri olan Yalıkavak'ta yer almaktadır. Deniz manzaralı, modern tasarımı ve lüks özellikleri ile dikkat çeken bu emlak, aileler için ideal bir yaşam alanı sunmaktadır."
        :features="['Havuz', 'Bahçe', 'Garaj', 'Balkon', 'Klima', 'Güvenlik', 'Asansör', 'Fitness']" :agent="[
            'name' => 'Ahmet Yılmaz',
            'phone' => '0533 209 03 02',
            'email' => 'ahmet@yalihanemlak.com',
            'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face',
            'rating' => 4.8,
            'properties' => 25,
        ]" :map-coordinates="[
            'lat' => 37.0581,
            'lng' => 27.258,
        ]" :show-map="true" :show-virtual-tour="true" :show-gallery="true"
        :show-share="true" />
@endsection
