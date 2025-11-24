<?php

namespace App\Services\Portal;

class PortalIdNormalizer
{
    public function normalize(array $raw): array
    {
        $ids = [];
        $sah = $raw['sahibinden_id'] ?? null;
        $ej = $raw['emlakjet_id'] ?? null;
        $hp = $raw['hepsiemlak_id'] ?? null;
        $zg = $raw['zingat_id'] ?? null;
        $he = $raw['hurriyetemlak_id'] ?? null;
        if ($sah) $ids['sahibinden'] = $this->normalizeProviderId('sahibinden', (string) $sah);
        if ($ej) $ids['emlakjet'] = $this->normalizeProviderId('emlakjet', (string) $ej);
        if ($hp) $ids['hepsiemlak'] = $this->normalizeProviderId('hepsiemlak', (string) $hp);
        if ($zg) $ids['zingat'] = $this->normalizeProviderId('zingat', (string) $zg);
        if ($he) $ids['hurriyetemlak'] = $this->normalizeProviderId('hurriyetemlak', (string) $he);
        return ['portal_ids' => $ids];
    }

    public function normalizeProviderId(string $portal, string $id): string
    {
        $v = trim($id);
        switch ($portal) {
            case 'sahibinden':
                $v = preg_replace('/\s+/', '', $v);
                $v = strtoupper($v);
                $v = preg_replace('/[^0-9-]/', '', $v);
                return $v;
            default:
                return $v;
        }
    }
}