<?php

namespace App\Services;

use App\Models\ExchangeRate;
use App\Services\Cache\CacheHelper;
use App\Services\Logging\LogService;
use Illuminate\Support\Facades\Http;

/**
 * TCMB (Türkiye Cumhuriyet Merkez Bankası) Currency Service
 *
 * Otomatik günlük döviz kuru çekme ve güncelleme
 *
 * Context7: Real-time currency rates for international listings
 */
class TCMBCurrencyService
{
    protected string $tcmbUrl = 'https://www.tcmb.gov.tr/kurlar/today.xml';

    protected array $supportedCurrencies = ['USD', 'EUR', 'GBP', 'CHF', 'CAD', 'AUD', 'JPY'];

    /**
     * Get today's exchange rates from TCMB
     *
     * @return array|null
     */
    public function getTodayRates()
    {
        // ✅ STANDARDIZED: Using CacheHelper
        return CacheHelper::remember(
            'currency',
            'tcmb_rates_today',
            'medium', // 1 hour
            function () {
                try {
                    // TCMB XML endpoint
                    $response = Http::timeout(10)->get($this->tcmbUrl);

                    if (! $response->successful()) {
                        // ✅ STANDARDIZED: Using LogService
                        LogService::warning('TCMB API error', ['status' => $response->status()]);

                        return $this->getFallbackRates();
                    }

                    // Parse XML
                    $xml = simplexml_load_string($response->body());
                    if (! $xml) {
                        return $this->getFallbackRates();
                    }

                    $rates = [];
                    $date = (string) $xml['Date'] ?? now()->format('d.m.Y');

                    foreach ($xml->Currency as $currency) {
                        $code = (string) $currency['CurrencyCode'];

                        if (in_array($code, $this->supportedCurrencies)) {
                            $rates[$code] = [
                                'code' => $code,
                                'name' => (string) $currency->CurrencyName,
                                'forex_buying' => (float) $currency->ForexBuying,
                                'forex_selling' => (float) $currency->ForexSelling,
                                'banknote_buying' => (float) $currency->BanknoteBuying,
                                'banknote_selling' => (float) $currency->BanknoteSelling,
                                'date' => $date,
                                'source' => 'TCMB',
                            ];
                        }
                    }

                    return $rates;
                } catch (\Exception $e) {
                    // ✅ STANDARDIZED: Using LogService
                    LogService::error('TCMB API exception', [], $e);

                    return $this->getFallbackRates();
                }
            });
    }

    /**
     * Update exchange rates in database
     *
     * @return int Number of rates updated
     */
    public function updateRates()
    {
        $rates = $this->getTodayRates();

        if (! $rates) {
            // ✅ STANDARDIZED: Using LogService
            LogService::warning('TCMB rates empty, skipping update');

            return 0;
        }

        $updated = 0;

        foreach ($rates as $code => $rate) {
            try {
                ExchangeRate::updateOrCreate(
                    [
                        'currency_code' => $code,
                        'rate_date' => now()->toDateString(),
                    ],
                    [
                        'rate_to_try' => $rate['forex_selling'], // TRY karşılığı
                        'buying_rate' => $rate['forex_buying'],
                        'selling_rate' => $rate['forex_selling'],
                        'source' => 'TCMB',
                        'updated_at' => now(),
                    ]
                );

                $updated++;
            } catch (\Exception $e) {
                // ✅ STANDARDIZED: Using LogService
                LogService::error("Failed to update rate for {$code}", ['code' => $code], $e);
            }
        }

        // Clear cache
        // ✅ STANDARDIZED: Using CacheHelper
        CacheHelper::forget('currency', 'tcmb_rates_today');
        CacheHelper::forget('currency', 'exchange_rates_latest');

        // ✅ STANDARDIZED: Using LogService
        LogService::info('TCMB rates updated', ['count' => $updated]);

        return $updated;
    }

    /**
     * Get exchange rate for a currency
     *
     * @param  string  $currencyCode  Currency code (USD, EUR, etc.)
     * @param  string  $type  Rate type (buying, selling, average)
     * @return float|null
     */
    public function getRate($currencyCode, $type = 'selling')
    {
        $rates = $this->getTodayRates();

        if (! isset($rates[$currencyCode])) {
            return null;
        }

        return match ($type) {
            'buying' => $rates[$currencyCode]['forex_buying'],
            'selling' => $rates[$currencyCode]['forex_selling'],
            'average' => ($rates[$currencyCode]['forex_buying'] + $rates[$currencyCode]['forex_selling']) / 2,
            default => $rates[$currencyCode]['forex_selling']
        };
    }

    /**
     * Convert amount to TRY
     *
     * @param  float  $amount  Amount
     * @param  string  $fromCurrency  From currency code
     * @return float Amount in TRY
     */
    public function convertToTRY($amount, $fromCurrency)
    {
        if ($fromCurrency === 'TRY') {
            return $amount;
        }

        $rate = $this->getRate($fromCurrency, 'selling');

        if (! $rate) {
            // ✅ STANDARDIZED: Using LogService
            LogService::warning("No rate found for {$fromCurrency}", ['currency' => $fromCurrency]);

            return $amount; // Return original if rate not found
        }

        return $amount * $rate;
    }

    /**
     * Convert amount from TRY to other currency
     *
     * @param  float  $amount  Amount in TRY
     * @param  string  $toCurrency  To currency code
     * @return float Converted amount
     */
    public function convertFromTRY($amount, $toCurrency)
    {
        if ($toCurrency === 'TRY') {
            return $amount;
        }

        $rate = $this->getRate($toCurrency, 'buying');

        if (! $rate) {
            return $amount;
        }

        return $amount / $rate;
    }

    /**
     * Get rate history for a currency
     *
     * @param  string  $currencyCode  Currency code
     * @param  int  $days  Number of days
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRateHistory($currencyCode, $days = 30)
    {
        return ExchangeRate::where('currency_code', $currencyCode)
            ->where('rate_date', '>=', now()->subDays($days))
            ->orderBy('rate_date', 'desc')
            ->get();
    }

    /**
     * Get fallback rates (last known rates from database)
     *
     * @return array
     */
    private function getFallbackRates()
    {
        $fallback = [];

        foreach ($this->supportedCurrencies as $code) {
            $latest = ExchangeRate::where('currency_code', $code)
                ->latest('rate_date')
                ->first();

            if ($latest) {
                $fallback[$code] = [
                    'code' => $code,
                    'forex_buying' => $latest->buying_rate,
                    'forex_selling' => $latest->selling_rate,
                    'date' => $latest->rate_date,
                    'source' => 'Database (Fallback)',
                ];
            }
        }

        return $fallback;
    }

    /**
     * Get all supported currencies
     *
     * @return array
     */
    public function getSupportedCurrencies()
    {
        return $this->supportedCurrencies;
    }

    /**
     * Get currency symbol
     *
     * @param  string  $code  Currency code
     * @return string
     */
    public function getCurrencySymbol($code)
    {
        return match ($code) {
            'TRY' => '₺',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'CHF' => 'CHF',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'JPY' => '¥',
            default => $code
        };
    }
}
