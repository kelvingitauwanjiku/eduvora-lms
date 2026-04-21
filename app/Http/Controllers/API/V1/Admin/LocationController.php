<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function countries()
    {
        $countries = Country::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $countries,
        ]);
    }

    public function states(Request $request)
    {
        $query = State::query();

        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        $states = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $states,
        ]);
    }

    public function cities(Request $request)
    {
        $query = City::query();

        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->has('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        $cities = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $cities,
        ]);
    }

    public function getByZip(Request $request)
    {
        $validated = $request->validate([
            'zip_code' => 'required|string',
            'country_code' => 'nullable|string|max:2',
        ]);

        $city = City::where('zip_code', $validated['zip_code'])
            ->when($validated['country_code'] ?? null, fn ($q, $code) => $q->whereHas('country', fn ($cq) => $cq->where('code', $code)))
            ->first();

        if (! $city) {
            return response()->json([
                'success' => false,
                'error' => 'Location not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $city->load('state', 'country'),
        ]);
    }

    public function storeCountry(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:2',
            'phone_code' => 'nullable|string|max:10',
            'currency' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        $country = Country::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Country created successfully',
            'data' => $country,
        ], 201);
    }

    public function storeState(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'code' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        $state = State::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'State created successfully',
            'data' => $state,
        ], 201);
    }

    public function storeCity(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'zip_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_active' => 'boolean',
        ]);

        $city = City::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'City created successfully',
            'data' => $city,
        ], 201);
    }
}
