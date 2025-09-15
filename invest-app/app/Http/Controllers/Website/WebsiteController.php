<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sector;
use App\Models\ChildSector;
use Illuminate\Support\Facades\Http;

class WebsiteController extends Controller
{
    public function index()
    {
        $sectors = Sector::all();
        return response()->json($sectors);
    }

public function getIlocosData()
{
    $provinceCode = "012800000";
    $citiesUrl = "https://psgc.gitlab.io/api/provinces/{$provinceCode}/cities-municipalities/";
    $citiesResponse = Http::get($citiesUrl);

    if ($citiesResponse->failed()) {
        return response()->json(['error' => 'Failed to fetch cities/municipalities'], 500);
    }

    $cities = collect($citiesResponse->json());

    // Hardcoded district mapping
    $districts = [
        '1st District' => [
            'City of Laoag', 'Adams', 'Bacarra', 'Bangui', 'Burgos',
            'Carasi', 'Dumalneg', 'Pagudpud', 'Pasuquin',
            'Piddig', 'Sarrat', 'Vintar'
        ],
        '2nd District' => [
            'City of Batac', 'Badoc', 'Banna', 'Currimao', 'Dingras',
            'Marcos', 'Nueva Era', 'Paoay', 'Pinili',
            'San Nicolas', 'Solsona'
        ],
    ];

    $data = $cities->map(function ($city) use ($districts) {
        // get barangays directly via city/municipality code
        $barangayResponse = Http::get("https://psgc.gitlab.io/api/cities-municipalities/{$city['code']}/barangays/");

        $barangayCount = 0;
        if ($barangayResponse->ok()) {
            $barangayCount = count($barangayResponse->json());
        }

        // Match district
        $districtName = null;
        foreach ($districts as $district => $places) {
            if (in_array($city['name'], $places)) {
                $districtName = $district;
                break;
            }
        }

        return [
            'name' => $city['name'],
            'isCapital' => $city['isCapital'],
            'isCity' => $city['isCity'],
            'isMunicipality' => $city['isMunicipality'],
            'district' => $districtName ?? 'Unknown',
            'numberBarangay' => $barangayCount,
        ];
    });

    return response()->json($data->values());
}

}
