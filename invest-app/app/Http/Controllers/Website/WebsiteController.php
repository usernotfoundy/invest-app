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

        $data = $cities->map(function ($city) {
            // get barangays directly via city/municipality code
            $barangayResponse = Http::get("https://psgc.gitlab.io/api/cities-municipalities/{$city['code']}/barangays/");

            $barangayCount = 0;
            if ($barangayResponse->ok()) {
                $barangayCount = count($barangayResponse->json());
            }

            // get district name
            // $districtResponse = Http::get("https://psgc.gitlab.io/api/districts/{$city['districtCode']}/");
            // $districtName = $districtResponse->ok()
            //     ? $districtResponse->json()['name'] ?? $city['districtCode']
            //     : $city['districtCode'];

            return [
                'name' => $city['name'],
                'isCapital' => $city['isCapital'],
                'isCity' => $city['isCity'],
                'isMunicipality' => $city['isMunicipality'],
                // 'district' => $districtName,
                'numberBarangay' => $barangayCount,
            ];
        });

        return response()->json($data->values());
    }
}
