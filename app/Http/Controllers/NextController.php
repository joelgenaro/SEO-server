<?php

namespace App\Http\Controllers;

use Illuminate\Cache\RetrievesMultipleKeys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NextController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = DB::table('companies')
            ->orderBy(DB::raw('ISNULL(location_country), location_country'), 'ASC')
            ->orderBy(DB::raw('ISNULL(region), region'), 'ASC')
            ->orderBy(DB::raw('ISNULL(locality), locality'), 'ASC')
            ->paginate(10);

        $countries = DB::table('companies')
            ->select('location_country')
            ->whereNotNull('location_country')
            ->orderBy('location_country', 'ASC')
            ->distinct()
            ->get();

        return ['data' => $data, 'countries' => $countries];
    }

    /**
     * get search options
     *
     */
    public function getSearchOptions($type, $value)
    {
        //
        $childrenType = null;
        $parentType = null;
        $sectorOne = null;
        $sectorTwo = null;
        $data = null;

        switch ($type) {
            case 'country':
                $childrenType = 'region';
                $parentType = "location_country";
                break;

            case 'city':
                $childrenType = 'locality';
                $parentType = "region";
                break;

            default:
                # code...
                break;
        }

        $sectorOne = DB::table('companies')
            ->select('industry')
            ->where($parentType, '=', $value)
            ->whereNotNull('industry')
            ->orderBy('industry', 'ASC')
            ->distinct()
            ->get();

        $sectorTwo = DB::table('companies')
            ->select('industry_two')
            ->where($parentType, '=', $value)
            ->whereNotNull('industry_two')
            ->orderBy('industry_two', 'ASC')
            ->distinct()
            ->get();

        $data = DB::table('companies')
            ->select($childrenType)
            ->where($parentType, '=', $value)
            ->whereNotNull($childrenType)
            ->orderBy($childrenType, 'ASC')
            ->distinct()
            ->get();

        return ['data' => $data, 'sectorOne' => $sectorOne, 'sectorTwo' => $sectorTwo];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getData(Request $request)
    {
        //
        $country = trim($request->country, " ");
        $city = trim($request->city, " ");
        $town = trim($request->town, " ");
        $sectorOne = trim($request->sectorOne, " ");
        $sectorTwo = trim($request->sectorTwo, " ");

        $data = DB::table('companies')
            ->when($country, function ($query, $country) {
                $query->where('location_country', '=', $country);
            })
            ->when($city, function ($query, $city) {
                $query->where('region', '=', $city);
            })
            ->when($town, function ($query, $town) {
                $query->where('locality', '=', $town);
            })
            ->when($sectorOne, function ($query, $sectorOne) {
                $query->where('industry', '=', $sectorOne);
            })
            ->when($sectorTwo, function ($query, $sectorTwo) {
                $query->where('industry_two', '=', $sectorTwo);
            })
            ->orderBy(DB::raw('ISNULL(location_country), location_country'), 'ASC')
            ->orderBy(DB::raw('ISNULL(region), region'), 'ASC')
            ->orderBy(DB::raw('ISNULL(locality), locality'), 'ASC')
            ->paginate(10);

        return $data;
    }

    public function getDataWithText(Request $request)
    {

        $sector = null;
        $city = null;

        $sector = $request->sector;
        $city = $request->city;

        $data = DB::table('companies')
            ->Where('industry', 'like', '%' . $sector . '%')
            ->orWhere('industry_two', 'like', '%' . $sector . '%')
            ->Where('region', 'like', '%' . $city . '%')
            ->orWhere('locality', 'like', '%' . $city . '%')
            ->orderBy(DB::raw('ISNULL(location_country), location_country'), 'ASC')
            ->orderBy(DB::raw('ISNULL(region), region'), 'ASC')
            ->orderBy(DB::raw('ISNULL(locality), locality'), 'ASC')
            ->paginate(10);

        return $data;

    }
}