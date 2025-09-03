<?php

namespace App\Http\Controllers\Encoding;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sector;
use App\Models\ChildSector;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DataInputController extends Controller
{
    public function addChildData(Request $request)
    {
        try {
            $validated = $request->validate([
                'sector_id' => 'required|exists:sectors,id',
                'child_id' => 'required|exists:child_sectors,id',
                'data' => 'required|array',
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

        // Get existing data as array
        $existingData = $sectorChild->data ?? [];
        if (is_string($existingData)) {
            $existingData = json_decode($existingData, true) ?? [];
        }
        if (!is_array($existingData)) {
            $existingData = [];
        }

        // Next ID: if empty => 1, else max+1
        $maxId = 0;
        foreach ($existingData as $item) {
            if (isset($item['id']) && is_numeric($item['id'])) {
                $maxId = max($maxId, (int) $item['id']);
            }
        }
        $newId = $maxId > 0 ? $maxId + 1 : 1;

        // Load template as array
        $template = $sectorChild->data_template ?? [];
        if (is_string($template)) {
            $template = json_decode($template, true) ?? [];
        }
        if (!is_array($template)) {
            $template = [];
        }

        // If template is a list of field names, convert to key=>null
        if (array_values($template) === $template) {
            $template = array_fill_keys($template, null);
        }

        // Ensure template cannot override id
        if (array_key_exists('id', $template)) {
            unset($template['id']);
        }

        // Merge: template -> provided data -> id (id last wins)
        $newData = array_merge($template, $validated['data'], ['id' => $newId]);

        // Append and save
        $existingData[] = $newData;
        $sectorChild->data = $existingData;
        $sectorChild->save();

        return response()->json([
            'message' => 'Data appended successfully',
            'child' => $sectorChild,
        ]);
    }

    public function updateChildData(Request $request)
{
    try {
        $validated = $request->validate([
            'sector_id' => 'required|exists:sectors,id',
            'child_id' => 'required|exists:child_sectors,id',
            'data_id' => 'required',
            'updates' => 'required|array'
        ]);
    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    }

    $child = ChildSector::where('id', $validated['child_id'])
        ->where('sector_id', $validated['sector_id'])
        ->firstOrFail();

    // Decode stored data
    $dataArray = is_string($child->data) ? json_decode($child->data, true) : $child->data;
    if (!is_array($dataArray)) {
        return response()->json(['message' => 'Invalid data format'], 400);
    }

    // Decode expected headers from template
    $expectedHeaders = json_decode($child->data_template, true) ?? [];
    if (!is_array($expectedHeaders)) {
        return response()->json(['message' => 'Invalid template format'], 400);
    }

    // Filter updates → only allow keys that exist in the template
    $filteredUpdates = array_intersect_key(
        $validated['updates'],
        array_flip($expectedHeaders)
    );

    $found = false;
    foreach ($dataArray as &$item) {
        if (isset($item['id']) && $item['id'] == $validated['data_id']) {
            // Merge only allowed keys
            $item = array_merge($item, $filteredUpdates);
            $found = true;
            break;
        }
    }

    if (!$found) {
        return response()->json(['message' => 'Data with given id not found'], 404);
    }

    // Save back
    $child->data = json_encode($dataArray);
    $child->save();

    return response()->json([
        'message' => 'Child data updated successfully',
        'child' => [
            'id' => $child->id,
            'name' => $child->name,
            'data' => $dataArray
        ]
    ]);
}


    public function viewSectorChildren($sector_id)
    {
        // $sector = Sector::with('children:id,name,sector_id')
        //                 ->findOrFail($sector_id);

        $sector = Sector::with('children')->findOrFail($sector_id);

        return response()->json([
            'sector_name' => $sector->name,
            'children' => $sector->children
        ]);
    }

    public function viewChildData($child_id)
    {
        $child = ChildSector::findOrFail($child_id);

        return response()->json([
            'id' => $child->id,
            'child_name' => $child->name,
            'data' => $child->data
        ]);
    }

    public function downloadChildTemplate($child_id)
    {
        $child = ChildSector::findOrFail($child_id);

        if (!$child->file_path) {
            return response()->json(['message' => 'No template available'], 404);
        }

        // Check if file exists on the "public" disk
        if (!Storage::disk('public')->exists($child->file_path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        // Stream download from storage
        return Storage::disk('public')->download($child->file_path);
    }

    public function uploadChildData(Request $request)
    {
        $validated = $request->validate([
            'file_upload' => 'required|mimes:xlsx,csv',
            'sector_id'   => 'required|exists:sectors,id',
            'child_id'    => 'required|exists:child_sectors,id',
        ]);

        $child = ChildSector::where('id', $request->child_id)
            ->where('sector_id', $request->sector_id)
            ->firstOrFail();

        // ✅ Already an array thanks to $casts
        $expectedHeaders = $child->data_template;

        // Load uploaded file
        $spreadsheet = IOFactory::load($request->file('file_upload')->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (count($rows) < 2) {
            return response()->json(['message' => 'File must contain headers and at least one row of data'], 422);
        }

        // Extract first row as uploaded headers
        $uploadedHeaders = array_values($rows[1]);

        // STRICT check: same order, same count, same values
        if ($uploadedHeaders !== $expectedHeaders) {
            return response()->json([
                'message'  => 'Template headers do not match',
                'expected' => $expectedHeaders,
                'uploaded' => $uploadedHeaders,
            ], 422);
        }

        // Collect data rows after header
        $dataToAppend = [];
        foreach (array_slice($rows, 1) as $row) {
            if (!array_filter($row)) {
                continue; // Skip empty rows
            }
            $rowAssoc = array_combine($expectedHeaders, array_values($row));
            $dataToAppend[] = $rowAssoc;
        }

        // Merge with existing data (already array because of $casts)
        $existingData = $child->data ?? [];
        $mergedData   = array_merge($existingData, $dataToAppend);

        $child->data = $mergedData; // Eloquent will json_encode automatically
        $child->save();

        return response()->json([
            'message'    => 'Data uploaded and appended successfully',
            'rows_added' => count($dataToAppend),
            'total_rows' => count($mergedData),
        ]);
    }

    public function deleteChildData(Request $request)
{
    try {
        $validated = $request->validate([
            'sector_id' => 'required|exists:sectors,id',
            'child_id'  => 'required|exists:child_sectors,id',
            'data_id'   => 'required'
        ]);
    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    }

    $child = ChildSector::where('id', $validated['child_id'])
        ->where('sector_id', $validated['sector_id'])
        ->firstOrFail();

    // Decode stored data
    $dataArray = is_string($child->data) ? json_decode($child->data, true) : $child->data;
    if (!is_array($dataArray)) {
        return response()->json(['message' => 'Invalid data format'], 400);
    }

    // Find & delete row by data_id
    $originalCount = count($dataArray);
    $dataArray = array_filter($dataArray, function ($item) use ($validated) {
        return !(isset($item['id']) && $item['id'] == $validated['data_id']);
    });

    // If no row was removed
    if (count($dataArray) === $originalCount) {
        return response()->json(['message' => 'Data with given id not found'], 404);
    }

    // Reindex array after filtering
    $dataArray = array_values($dataArray);

    // Save updated JSON
    $child->data = json_encode($dataArray);
    $child->save();

    return response()->json([
        'message' => 'Row deleted successfully',
        'total_rows' => count($dataArray),
        'child' => [
            'id' => $child->id,
            'name' => $child->name
        ]
    ]);
}

}
