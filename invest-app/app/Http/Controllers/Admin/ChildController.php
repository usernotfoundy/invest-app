<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChildSector;
use App\Models\Sector;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ChildController extends Controller
{
    public function createChild(Request $request)
    {
        try {
            $validated = $request->validate([
                'sector_id' => ['required', 'exists:sectors,id'],
                'name' => ['required', 'string', 'max:255'],
                'data_template' => ['required', 'array'], 
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $child = ChildSector::create([
            'sector_id' => $validated['sector_id'],
            'name' => $validated['name'],
            'data' => [],
            'data_template' => $validated['data_template'],
        ]);

        // Create Excel file
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Normalize template → always key=>value array
        $template = $validated['data_template'];
        if (array_values($template) === $template) {
            // It’s a list like ["commodity", "yield"]
            $columns = $template;
        } else {
            // It’s an object like {"commodity": null, "yield": null}
            $columns = array_keys($template);
        }

        // Write column headers (first row)
        foreach ($columns as $index => $colName) {
            $sheet->setCellValueByColumnAndRow($index + 1, 1, $colName);
        }

        // Save file to storage/app/public/child_templates/
        $directory = 'child_templates';
        $fileName = $child->name . '_template.xlsx';
        $filePath = $directory . '/' . $fileName;

        Storage::disk('public')->makeDirectory($directory);

        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/public/' . $filePath));

        // Save relative path to DB
        $child->file_path = $filePath;
        $child->save();

        return response()->json([
            'message' => 'Child sector created successfully',
            'data' => $child,
        ], 201);
    }

    public function listChildren($sector_id)
    {
        $sector = Sector::with('children')->findOrFail($sector_id);

        return response()->json([
            'sector_name' => $sector->name,
            'children' => $sector->children
        ]);
    }

    public function clearData(Request $request)
    {
        try {
            $validated = $request->validate([
                'child_id' => 'required|exists:child_sectors,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $sectorChild = ChildSector::findOrFail($validated['child_id']);

        $sectorChild->data = []; // Clear data
        $sectorChild->save();

        return response()->json([
            'message' => 'Data cleared successfully',
            'child' => $sectorChild
        ]);
    }

    public function viewChildData($child_id)
    {
        $child = ChildSector::findOrFail($child_id);

        return response()->json([
            'child_name' => $child->name,
            'data' => $child->data
        ]);
    }

    public function destroyChild(Request $request)
    {
        try {
            $validated = $request->validate([
                'child_id' => 'required|exists:child_sectors,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $child = ChildSector::findOrFail($validated['child_id']);

        // Delete the template file if it exists
        if ($child->file_path && Storage::disk('public')->exists($child->file_path)) {
            Storage::disk('public')->delete($child->file_path);
        }

        $child->delete();

        return response()->json([
            'message' => 'Child deleted successfully',
        ]);
    }

    public function updateTemplate(Request $request)
    {
        try {
            $validated = $request->validate([
                'child_id' => 'required|exists:child_sectors,id',
                'updates' => ['required', 'array'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $child = ChildSector::findOrFail($validated['child_id']);

        $template = $child->data_template ?? [];

        // Merge new/updated keys
        foreach ($request->updates as $key => $value) {
            $template[$key] = $value;
        }

        $child->data_template = $template;
        $child->save();

        return response()->json([
            'message' => 'Data template updated successfully',
            'data_template' => $child->data_template
        ]);
    }

    public function viewDataTemplate(Request $request)
    {
        try {
            $validated = $request->validate([
                'child_id' => 'required|exists:child_sectors,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $child = ChildSector::findOrFail($validated['child_id']);

        return response()->json([
            'child_name' => $child->name,
            'data_template' => $child->data_template
        ]);
    }

}

