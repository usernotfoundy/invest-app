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
        // $sectors = Sector::all();
        // $profiles = InProfile::select('id', 'profile_name', 'data')->get();
        $profiles = InProfile::all();
        return response()->json($profiles);
    }

    public function destroy($id)
    {
        $profile = InProfile::findOrFail($id);
        $profile->delete();

        return response()->json(['message' => 'profile deleted successfully']);
    }

public function economicIndicator($id)
{
    $profile = InProfile::findOrFail($id);

    $years = collect($profile->data)->pluck('year');

    // Example: extract multiple datasets
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

}
