<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use App\Services\Currency\ExchangeRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    protected ExchangeRateService $exchangeRateService;

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }

    public function index(Request $request): JsonResponse
    {
        $currencies = $this->exchangeRateService->getSupportedCurrencies();
        
        $selected = $request->user() 
            ? $request->user()->currency 
            : 'USD';

        return response()->json([
            'currencies' => $currencies,
            'selected' => $selected,
        ]);
    }

    public function rates(Request $request): JsonResponse
    {
        $base = $request->get('base', 'USD');
        $rates = $this->exchangeRateService->getAllRates($base);

        return response()->json([
            'base' => $base,
            'rates' => $rates,
            'updated_at' => now(),
        ]);
    }

    public function convert(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
        ]);

        $result = $this->exchangeRateService->convertWithCurrency(
            $request->amount,
            $request->from,
            $request->to
        );

        return response()->json($result);
    }

    public function refresh(): JsonResponse
    {
        $rates = $this->exchangeRateService->refreshRates();

        return response()->json([
            'message' => 'Exchange rates refreshed',
            'rates' => $rates,
            'updated_at' => now(),
        ]);
    }

    public function getExchangeRateService(): ExchangeRateService
    {
        return $this->exchangeRateService;
    }
}