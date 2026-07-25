<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ZoneService;

class ZoneController extends Controller
{
    public function __construct(private ZoneService $zones) {}

    public function index()
    {
        return response()->json(['data' => $this->zones->listAllWithHotels()]);
    }
}
