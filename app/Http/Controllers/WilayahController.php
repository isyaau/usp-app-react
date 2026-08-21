<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\Village;

/**
 * Endpoint data wilayah Indonesia (laravolt/indonesia)
 * untuk dropdown berantai Provinsi → Kota → Kecamatan → Kelurahan.
 */
class WilayahController extends Controller
{
    public function provinces(): JsonResponse
    {
        return response()->json(
            Province::orderBy('name')
                ->get(['code', 'name'])
        );
    }

    public function cities(string $provinceCode): JsonResponse
    {
        return response()->json(
            City::where('province_code', $provinceCode)
                ->orderBy('name')
                ->get(['code', 'name'])
        );
    }

    public function districts(string $cityCode): JsonResponse
    {
        return response()->json(
            District::where('city_code', $cityCode)
                ->orderBy('name')
                ->get(['code', 'name'])
        );
    }

    public function villages(string $districtCode): JsonResponse
    {
        return response()->json(
            Village::where('district_code', $districtCode)
                ->orderBy('name')
                ->get(['code', 'name'])
        );
    }
}
