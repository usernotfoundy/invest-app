<?php

namespace App\Http\Controllers\PowerBI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sector;
use App\Models\ChildSector;

class PowerBIController extends Controller
{
    public function getData(Request $request)
    {
        try {
            $validated = $request->validate([
                'sector_id' => 'required|exists:sectors,id',
                'child_id' => 'required|exists:child_sectors,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $sectorChild = ChildSector::where('id', $validated['child_id'])
            ->where('sector_id', $validated['sector_id'])
            ->firstOrFail();

        return response()->json($sectorChild->data);
    }
}
