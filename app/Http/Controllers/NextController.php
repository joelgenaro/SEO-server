<?php

namespace App\Http\Controllers;

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
            ->orderBy(DB::raw('ISNULL(location), location'), 'ASC')
            ->orderBy(DB::raw('ISNULL(metro), metro'), 'ASC')
            ->orderBy(DB::raw('ISNULL(region), region'), 'ASC')
            ->orderBy(DB::raw('ISNULL(locality), locality'), 'ASC')
            ->orderBy(DB::raw('ISNULL(industry), industry'), 'ASC')
            ->orderBy(DB::raw('ISNULL(industry_two), industry_two'), 'ASC')
            ->paginate(10);

        $countries = DB::table('companies')
            ->select('location')
            ->whereNotNull('location')
            ->orderBy('location', 'ASC')
            ->distinct()
            ->get();

        $sectorOne = DB::table('companies')
            ->select('industry')
            ->whereNotNull('industry')
            ->orderBy('industry', 'ASC')
            ->distinct()
            ->get();

        $sectorTwo = DB::table('companies')
            ->select('industry_two')
            ->whereNotNull('industry_two')
            ->orderBy('industry_two', 'ASC')
            ->distinct()
            ->get();

        return ['data' => $data, 'countries' => $countries, 'sectorOne' => $sectorOne, 'sectorTwo' => $sectorTwo];
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

        switch ($type) {
            case 'country':
                $childrenType = 'metro';
                $parentType = "location";
                break;
            case 'city':
                $childrenType = 'region';
                $parentType = "metro";

                break;
            case 'town':
                $childrenType = 'locality';
                $parentType = "region";

                break;

            default:
                # code...
                break;
        }

        $data = DB::table('companies')
            ->select($childrenType)
            ->where($parentType, '=', $value)
            ->whereNotNull($childrenType)
            ->orderBy($childrenType, 'ASC')
            ->distinct()
            ->get();

        return $data;
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
        $location = null;
        $metro = null;
        $region = null;
        $locality = null;
        $industry = null;
        $industry_two = null;
        $industry_three = null;

        $location = $request->location;
        $metro = $request->metro;
        $region = $request->region;
        $locality = $request->locality;
        $industry = $request->industry;
        $industry_two = $request->industry_two;
        $industry_three = $request->industry_three;

        $data = DB::table('companies')
            ->when($location, function ($query, $location) {
                $query->where('location', $location);
            })
            ->when($metro, function ($query, $metro) {
                $query->where('metro', $metro);
            })
            ->when($region, function ($query, $region) {
                $query->where('region', $region);
            })
            ->when($locality, function ($query, $locality) {
                $query->where('locality', $locality);
            })
            ->when($industry, function ($query, $industry) {
                $query->where('industry', $industry);
            })
            ->when($industry_two, function ($query, $industry_two) {
                $query->where('industry_two', $industry_two);
            })
            ->when($industry_three, function ($query, $industry_three) {
                $query->where('industry_three', $industry_three);
            })
            ->orderBy(DB::raw('ISNULL(location), location'), 'ASC')
            ->orderBy(DB::raw('ISNULL(metro), metro'), 'ASC')
            ->orderBy(DB::raw('ISNULL(region), region'), 'ASC')
            ->orderBy(DB::raw('ISNULL(locality), locality'), 'ASC')
            ->orderBy(DB::raw('ISNULL(industry), industry'), 'ASC')
            ->orderBy(DB::raw('ISNULL(industry_two), industry_two'), 'ASC')
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
            ->orWhere('metro', 'like', '%' . $city . '%')
            ->orWhere('region', 'like', '%' . $city . '%')
            ->orderBy(DB::raw('ISNULL(metro), metro'), 'ASC')
            ->orderBy(DB::raw('ISNULL(region), region'), 'ASC')
            ->orderBy(DB::raw('ISNULL(industry), industry'), 'ASC')
            ->orderBy(DB::raw('ISNULL(industry_two), industry_two'), 'ASC')
            ->paginate(10);

        return $data;

    }
}