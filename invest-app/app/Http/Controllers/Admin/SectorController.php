<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sector;


class SectorController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:1000'],
                'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            // Define directory same way as createChild
            $directory = 'assets';

            // Generate filename
            $fileName = time() . '_' . $request->file('image')->getClientOriginalName();
            $filePath = $directory . '/' . $fileName;

            // Ensure directory exists in storage/app/public
            Storage::disk('public')->makeDirectory($directory);

            // Store file
            $request->file('image')->storeAs($directory, $fileName, 'public');

            // Save relative path to DB
            $imagePath = $filePath;
        }

        $sector = Sector::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'image_path' => $imagePath,
        ]);

        activity('create')
            ->performedOn($sector)
            ->causedBy(auth()->user())
            ->withProperties(['create' => $sector->getChanges()])
            ->log('created new sector');  

        return response()->json([
            'message' => 'Sector created successfully',
            'sector' => $sector,
        ], 201);
    }

    public function index()
    {
        $sectors = Sector::select('id', 'name')->get();
        return response()->json($sectors);
    }

    public function show($id)
    {
        $sector = Sector::with('children')->findOrFail($id);
        return response()->json($sector);
    }

    public function destroy($id)
    {
        $sector = Sector::findOrFail($id);
        $sector->delete();

        activity('delete')
            ->performedOn($sector)
            ->causedBy(auth()->user())
            ->withProperties(['delete' => $sector->getChanges()])
            ->log('delete sector');  

        return response()->json(['message' => 'Sector deleted successfully']);
    }

    public function update($id)
    {
        $sector = Sector::findOrFail($id);

        $sector->update([
            'name' => request('name'),
            'description' => request('description'),
        ]);

        activity('update')
            ->performedOn($sector)
            ->causedBy(auth()->user())
            ->withProperties(['update' => $sector->getChanges()])
            ->log('update sector');  

        return response()->json(['message' => 'Sector updated successfully', 'sector' => $sector]);
    }
    
    public function updateThumbnail(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:sectors,id'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $sector = Sector::findOrFail($request->id);

        // Remove old file if exists
        if ($sector->image_path && Storage::disk('public')->exists($sector->image_path)) {
            Storage::disk('public')->delete($sector->image_path);
        }

        // Save new file
        $directory = 'assets';
        $fileName = time() . '_' . $request->file('image')->getClientOriginalName();
        $filePath = $directory . '/' . $fileName;

        Storage::disk('public')->makeDirectory($directory);
        $request->file('image')->storeAs($directory, $fileName, 'public');

        // Update DB
        $sector->update([
            'image_path' => $filePath,
        ]);

        activity('update')
            ->performedOn($sector)
            ->causedBy(auth()->user())
            ->withProperties(['update' => $sector->getChanges()])
            ->log('update sector image thumbnail');  

        return response()->json([
            'message' => 'Thumbnail updated successfully',
            'sector' => $sector,
        ]);
    }

}
