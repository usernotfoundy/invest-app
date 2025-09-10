<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sector;
use App\Models\ChildSector;

class WebsiteController extends Controller
{
    public function index()
    {
        $sectors = Sector::all();
        return response()->json($sectors);
    }
}
