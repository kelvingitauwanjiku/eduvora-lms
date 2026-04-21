<?php

namespace App\Services\Currency;

use App\Models\Currency;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    private string $baseCurrency = 'USD';
    private int $cacheTtl = 3600;
    private array $providers = [];

    public function __construct()
    {
        $this->providers = [
            'primary' => config('services.exchange_rate.provider', 'exchangerate'),
            'fallback' => 'fixed',
        ];
    }

    public function getRate(string $from, string $to): float
    {
        if ($from === $to) {
            return 1.0;
        }

        if ($from === $this->baseCurrency) {
            return $this->getRateToBase($to);
        }

        if ($to === $this->baseCurrency) {
            return 1 / $this->getRateToBase($from);
        }

        $fromRate = $this->getRateToBase($from);
        $toRate = $this->getRateToBase($to);

        return $toRate / $fromRate;
    }

    public function convert(float $amount, string $from, string $to): float
    {
        $rate = $this->getRate($from, $to);
        return round($amount * $rate, 2);
    }

    public function convertWithCurrency(float $amount, string $fromCurrency, string $toCurrency): array
    {
        $rate = $this->getRate($fromCurrency, $toCurrency);
        $converted = round($amount * $rate, 2);

        return [
            'amount' => $amount,
            'from' => $fromCurrency,
            'to' => $toCurrency,
            'rate' => $rate,
            'converted_amount' => $converted,
            'symbol' => $this->getCurrencySymbol($toCurrency),
            'formatted' => $this->formatCurrency($converted, $toCurrency),
        ];
    }

    public function getAllRates(string $baseCurrency = 'USD'): array
    {
        $cacheKey = "exchange_rates:{$baseCurrency}";
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($baseCurrency) {
            try {
                return $this->fetchFromProvider($baseCurrency);
            } catch (\Exception $e) {
                return $this->getFixedRates($baseCurrency);
            }
        });
    }

    public function refreshRates(): array
    {
        Cache::forget("exchange_rates:{$this->baseCurrency}");
        
        try {
            return $this->fetchFromProvider($this->baseCurrency);
        } catch (\Exception $e) {
            throw new \Exception('Failed to refresh exchange rates: ' . $e->getMessage());
        }
    }

    protected function getRateToBase(string $currency): float
    {
        $rates = $this->getAllRates($this->baseCurrency);
        
        return $rates[$currency] ?? $this->getFixedRate($currency);
    }

    protected function fetchFromProvider(string $baseCurrency): array
    {
        $provider = config('services.exchange_rate.provider', 'exchangerate');
        
        return match ($provider) {
            'exchangerate' => $this->fetchFromExchangeRateAPI($baseCurrency),
            'openexchangerates' => $this->fetchFromOpenExchangeRates($baseCurrency),
            'frankfurter' => $this->fetchFromFrankfurter($baseCurrency),
            default => $this->getFixedRates($baseCurrency),
        };
    }

    protected function fetchFromExchangeRateAPI(string $baseCurrency): array
    {
        $apiKey = config('services.exchange_rate.api_key');
        $baseUrl = config('services.exchange_rate.url', 'https://v6.exchangerate-api.com/v6');
        
        $response = Http::get("{$baseUrl}/{$apiKey}/latest/{$baseCurrency}");
        
        if ($response->failed()) {
            throw new \Exception('Exchange Rate API request failed');
        }

        $data = $response->json();
        
        return $data['conversion_rates'] ?? [];
    }

    protected function fetchFromOpenExchangeRates(string $baseCurrency): array
    {
        $apiKey = config('services.exchange_rate.openexchangerates_key');
        
        $response = Http::get("https://openexchangerates.org/api/latest.json", [
            'app_id' => $apiKey,
            'base' => $baseCurrency,
        ]);
        
        if ($response->failed()) {
            throw new \Exception('Open Exchange Rates request failed');
        }

        return $response->json('rates');
    }

    protected function fetchFromFrankfurter(string $baseCurrency): array
    {
        $response = Http::get("https://api.frankfurter.app/latest", [
            'from' => $baseCurrency,
        ]);
        
        if ($response->failed()) {
            throw new \Exception('Frankfurter API request failed');
        }

        $data = $response->json();
        $rates = $data['rates'] ?? [];
        $rates[$baseCurrency] = 1;

        return $rates;
    }

    protected function getFixedRates(string $baseCurrency): array
    {
        $fixedRates = [
            'USD' => 1.0,
            'EUR' => 0.92,
            'GBP' => 0.79,
            'CAD' => 1.36,
            'AUD' => 1.53,
            'JPY' => 149.50,
            'INR' => 83.12,
            'BRL' => 4.97,
            'MXN' => 17.15,
            'SGD' => 1.34,
            'AED' => 3.67,
            'SAR' => 3.75,
            'NGN' => 770.00,
            'KES' => 153.50,
            'ZAR' => 18.75,
            'GHS' => 12.35,
        ];

        if ($baseCurrency !== 'USD') {
            $baseRate = $fixedRates[$baseCurrency] ?? 1.0;
            foreach ($fixedRates as $currency => $rate) {
                $fixedRates[$currency] = $rate / $baseRate;
            }
        }

        return $fixedRates;
    }

    protected function getFixedRate(string $currency): float
    {
        $fixedRates = $this->getFixedRates('USD');
        return $fixedRates[$currency] ?? 1.0;
    }

    public function getCurrencySymbol(string $currency): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'INR' => '₹',
            'BRL' => 'R$',
            'MXN' => '$',
            'CAD' => 'CA$',
            'AUD' => 'A$',
            'SGD' => 'S$',
            'AED' => 'د.إ',
            'SAR' => '﷼',
            'NGN' => '₦',
            'KES' => 'KSh',
            'ZAR' => 'R',
            'GHS' => '₵',
        ];

        return $symbols[$currency] ?? $currency;
    }

    public function formatCurrency(float $amount, string $currency): string
    {
        $symbol = $this->getCurrencySymbol($currency);
        
        return match ($currency) {
            'JPY', 'KRW' => "{$symbol}" . number_format($amount, 0, '.', ','),
            default => "{$symbol}" . number_format($amount, 2, '.', ','),
        };
    }

    public function getSupportedCurrencies(): array
    {
        return [
            ['code' => 'USD', 'name' => 'United States Dollar', 'symbol' => '$', 'enabled' => true],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'enabled' => true],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'enabled' => true],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => 'CA$', 'enabled' => true],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => 'A$', 'enabled' => true],
            ['code' => 'JPY', 'name' => 'Japanese Yen', 'symbol' => '¥', 'enabled' => true],
            ['code' => 'INR', 'name' => 'Indian Rupee', 'symbol' => '₹', 'enabled' => true],
            ['code' => 'BRL', 'name' => 'Brazilian Real', 'symbol' => 'R$', 'enabled' => true],
            ['code' => 'MXN', 'name' => 'Mexican Peso', 'symbol' => '$', 'enabled' => true],
            ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => 'S$', 'enabled' => false],
            ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'د.إ', 'enabled' => false],
            ['code' => 'SAR', 'name' => 'Saudi Riyal', 'symbol' => '﷼', 'enabled' => false],
            ['code' => 'NGN', 'name' => 'Nigerian Naira', 'symbol' => '₦', 'enabled' => false],
            ['code' => 'KES', 'name' => 'Kenyan Shilling', 'symbol' => 'KSh', 'enabled' => false],
            ['code' => 'ZAR', 'name' => 'South African Rand', 'symbol' => 'R', 'enabled' => false],
            ['code' => 'GHS', 'name' => 'Ghanaian Cedi', 'symbol' => '₵', 'enabled' => false],
        ];
    }
}