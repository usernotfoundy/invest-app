<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sector;


class SectorController extends Controller
{
    //
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:1000'],
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $sector = Sector::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'Sector created successfully', 'sector' => $sector], 201);
    }

    public function index()
    {
        // $sectors = Sector::all();
        $sectors = Sector::select('id', 'name')->get();
        return response()->json($sectors);
    }

    public function show($id)
    {
        $sector = Sector::findOrFail($id);
        return response()->json($sector);
    }

    public function destroy($id)
    {
        $sector = Sector::findOrFail($id);
        $sector->delete();

        return response()->json(['message' => 'Sector deleted successfully']);
    }

    public function update($id)
    {
        $sector = Sector::findOrFail($id);

        $sector->update([
            'name' => request('name'),
            'description' => request('description'),
        ]);

        return response()->json(['message' => 'Sector updated successfully', 'sector' => $sector]);
    }
}
