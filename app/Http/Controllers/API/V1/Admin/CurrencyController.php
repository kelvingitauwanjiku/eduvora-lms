<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $currencies,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:3|unique:currencies,code',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($request->boolean('is_default')) {
            Currency::where('is_default', true)->update(['is_default' => false]);
        }

        $currency = Currency::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Currency created successfully',
            'data' => $currency,
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $currency = Currency::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'code' => 'string|max:3|unique:currencies,code,'.$id,
            'symbol' => 'string|max:10',
            'exchange_rate' => 'numeric|min:0',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($request->boolean('is_default')) {
            Currency::where('is_default', true)->update(['is_default' => false]);
        }

        $currency->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Currency updated successfully',
            'data' => $currency,
        ]);
    }

    public function destroy(int $id)
    {
        $currency = Currency::findOrFail($id);

        if ($currency->is_default) {
            return response()->json([
                'success' => false,
                'error' => 'Cannot delete default currency',
            ], 400);
        }

        $currency->delete();

        return response()->json([
            'success' => true,
            'message' => 'Currency deleted successfully',
        ]);
    }

    public function setDefault(int $id)
    {
        Currency::where('is_default', true)->update(['is_default' => false]);
        $currency = Currency::findOrFail($id);
        $currency->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Default currency set',
        ]);
    }

    public function active()
    {
        $currencies = Currency::where('is_active', true)->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $currencies,
        ]);
    }
}
