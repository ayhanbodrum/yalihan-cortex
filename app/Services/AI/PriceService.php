<?php

namespace App\Services\AI;

class PriceService
{
    public function predict(array $propertyData): array
    {
        $area = (float)($propertyData['area'] ?? 0);
        $city = (string)($propertyData['city'] ?? '');
        $district = (string)($propertyData['district'] ?? '');
        $type = (string)($propertyData['type'] ?? 'konut');
        $unit = $this->unitPrice($city, $district, $type);
        $base = max(1, $area) * $unit;
        $adj = $this->adjustments($propertyData);
        $price = (int)round($base * $adj);
        $conf = $this->confidence($propertyData);
        return [
            'predicted_price' => $price,
            'currency' => 'TRY',
            'confidence' => $conf,
            'inputs' => [
                'area' => $area,
                'unit_price' => $unit,
                'adjustment' => $adj,
            ],
        ];
    }

    protected function unitPrice(string $city, string $district, string $type): int
    {
        $base = 15000;
        if ($type === 'arsa') $base = 5000;
        if ($type === 'isyeri') $base = 20000;
        $map = [
            'istanbul' => 25000,
            'izmir' => 18000,
            'ankara' => 17000,
            'muğla' => 22000,
            'bodrum' => 30000,
        ];
        $c = mb_strtolower($city);
        $d = mb_strtolower($district);
        foreach ($map as $k => $v) {
            if ($c === $k || $d === $k) { $base = $v; break; }
        }
        return $base;
    }

    protected function adjustments(array $data): float
    {
        $adj = 1.0;
        if (!empty($data['sea_view'])) $adj += 0.12;
        if (!empty($data['new_build'])) $adj += 0.08;
        if (!empty($data['needs_renovation'])) $adj -= 0.10;
        if (!empty($data['parking'])) $adj += 0.03;
        if (!empty($data['security'])) $adj += 0.02;
        return max(0.6, min($adj, 1.5));
    }

    protected function confidence(array $data): int
    {
        $score = 70;
        if (!empty($data['area'])) $score += 10;
        if (!empty($data['city'])) $score += 5;
        if (!empty($data['district'])) $score += 5;
        return (int)min(95, $score);
    }
}