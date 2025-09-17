<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InProfile;

class InProfileController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'profile_name' => ['required', 'string', 'max:255'],
                'data' => ['required', 'array'],
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $profile = InProfile::create([
            'profile_name' => $validated['profile_name'],
            'data' => $validated['data'],
        ]);


        return response()->json(['message' => 'profile added successfully', 'profile' => $profile]);
    }

    public function index()
    {
        $profiles = InProfile::select('id', 'profile_name')->get();
        return response()->json($profiles);
    }

    public function destroy($id)
    {
        $profile = InProfile::findOrFail($id);
        $profile->delete();

        return response()->json(['message' => 'profile deleted successfully']);
    }

    public function economicIndicator()
    {
        $profileName = "economic indicator";
        $profile = InProfile::select('profile_name', 'data')
            ->where('profile_name', $profileName)
            ->firstOrFail();

        $years = collect($profile->data)->pluck('year');

        $populations = collect($profile->data)->pluck('population');
        $purchasingPower = collect($profile->data)->pluck('purchasing_power');
        $income = collect($profile->data)->pluck('average_annual_family.income');
        $expenditure = collect($profile->data)->pluck('average_annual_family.expenditure');

        return response()->json([
            'labels' => $years,
            'datasets' => [
                [
                    'label' => 'Population',
                    'data' => $populations,
                ],
                [
                    'label' => 'Purchasing Power',
                    'data' => $purchasingPower,
                ],
                [
                    'label' => 'Average Family Income',
                    'data' => $income,
                ],
                [
                    'label' => 'Average Family Expenditure',
                    'data' => $expenditure,
                ],
            ],
        ]);
    }

    public function crimeEfficiency()
    {
        $profileName = "crime efficiency";
        $profile = InProfile::select('profile_name', 'data')
            ->where('profile_name', $profileName)
            ->firstOrFail();

        return response()->json($profile);
    }

    public function povertyIncidence()
    {
        $profileName = "poverty incidence";
        $profile = InProfile::select('profile_name', 'data')
            ->where('profile_name', $profileName)
            ->firstOrFail();

        return response()->json($profile);
    }

    public function literacyRate()
    {
        $profileName = "literacy rate";
        $profile = InProfile::select('profile_name', 'data')
            ->where('profile_name', $profileName)
            ->firstOrFail();

        return response()->json($profile);
    }
}
