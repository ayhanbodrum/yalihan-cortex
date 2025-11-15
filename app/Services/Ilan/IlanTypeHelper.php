<?php

namespace App\Services\Ilan;

use App\Models\Ilan;

class IlanTypeHelper
{
    public function getTypeColor(Ilan $ilan): string
    {
        if (!$ilan->kategori) {
            return 'bg-gray-100 text-gray-800';
        }

        $categoryName = strtolower($ilan->kategori->ad ?? '');

        $colorMap = [
            'satılık' => 'bg-green-100 text-green-800',
            'kiralık' => 'bg-blue-100 text-blue-800',
            'daire' => 'bg-purple-100 text-purple-800',
            'villa' => 'bg-orange-100 text-orange-800',
            'arsa' => 'bg-yellow-100 text-yellow-800',
            'ofis' => 'bg-gray-100 text-gray-800',
            'dükkan' => 'bg-red-100 text-red-800',
        ];

        foreach ($colorMap as $key => $color) {
            if (strpos($categoryName, $key) !== false) {
                return $color;
            }
        }

        return 'bg-gray-100 text-gray-800';
    }

    public function getTypeIcon(Ilan $ilan): string
    {
        if (!$ilan->kategori) {
            return 'fas fa-building';
        }

        $categoryName = strtolower($ilan->kategori->ad ?? '');

        $iconMap = [
            'daire' => 'fas fa-home',
            'villa' => 'fas fa-house-user',
            'arsa' => 'fas fa-mountain',
            'ofis' => 'fas fa-building',
            'dükkan' => 'fas fa-store',
            'depo' => 'fas fa-warehouse',
            'fabrika' => 'fas fa-industry',
        ];

        foreach ($iconMap as $key => $icon) {
            if (strpos($categoryName, $key) !== false) {
                return $icon;
            }
        }

        return 'fas fa-building';
    }

    public function getTypeSummary(Ilan $ilan): array
    {
        // Context7: Handle IlanStatus enum properly
        $statusValue = $ilan->status instanceof \App\Enums\IlanStatus
            ? $ilan->status->label()
            : ucfirst($ilan->status ?? 'Bilinmiyor');

        return [
            'type' => optional($ilan->kategori)->ad ?? 'Kategorisiz',
            'price' => number_format($ilan->fiyat) . ' ' . $ilan->para_birimi,
            'area' => ($ilan->net_metrekare ? $ilan->net_metrekare . ' m²' : 'Belirtilmemiş'),
            'category' => optional($ilan->kategori)->ad ?? 'Yok',
            'status' => $statusValue,
            'special' => $this->getSpecialBadge($ilan)
        ];
    }

    public function getTypeSpecificFields(Ilan $ilan): array
    {
        $fields = [];

        $fields['genel'] = [
            'title' => 'Genel Bilgiler',
            'icon' => 'fas fa-info-circle',
            'color' => 'blue',
            'fields' => [
                'fiyat' => [
                    'label' => 'Fiyat',
                    'value' => number_format($ilan->fiyat) . ' ' . $ilan->para_birimi,
                    'type' => 'price'
                ],
                'status' => [
                    'label' => 'Durum',
                    'value' => $ilan->status instanceof \App\Enums\IlanStatus
                        ? $ilan->status->label()
                        : ($ilan->status ?? 'Bilinmiyor'),
                    'type' => 'status'
                ],
                'ilan_tarihi' => [
                    'label' => 'İlan Tarihi',
                    'value' => $ilan->created_at ? $ilan->created_at->format('d.m.Y') : 'Belirtilmemiş',
                    'type' => 'date'
                ]
            ]
        ];

        return $fields;
    }

    public function getSpecialBadge(Ilan $ilan): ?string
    {
        // Context7: Handle IlanStatus enum properly
        $statusValue = $ilan->status instanceof \App\Enums\IlanStatus
            ? $ilan->status->value
            : ($ilan->status ?? '');

        if ($statusValue === 'yayinda' || $statusValue === 'Aktif' || $statusValue === \App\Enums\IlanStatus::YAYINDA->value) {
            return 'Yayında';
        }

        if ($statusValue === 'Aktif') {
            return 'Hazır';
        }

        if ($ilan->created_at && $ilan->created_at->isToday()) {
            return 'Yeni İlan';
        }

        return null;
    }
}
