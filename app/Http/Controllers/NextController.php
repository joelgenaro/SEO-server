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
            ->orderBy(DB::raw('ISNULL(industry), industry'), 'ASC')
            ->orderBy(DB::raw('ISNULL(industry_two), industry_two'), 'ASC')
            ->paginate(10);

        $countries = DB::table('companies')
            ->select('location')
            ->whereNotNull('location')
            ->orderBy('location', 'ASC')
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
        $data = null;

        switch ($type) {
            case 'country':
                $childrenType = 'metro';
                $parentType = "location";

                $sectorOne = DB::table('companies')
                    ->select('industry')
                    ->where('location', '=', $value)
                    ->whereNotNull('industry')
                    ->orderBy('industry', 'ASC')
                    ->distinct()
                    ->get();
                break;

            case 'city':
                $childrenType = 'region';
                $parentType = "metro";
                break;

            case 'sectorOne':
                $childrenType = 'industry_two';
                $parentType = "industry";
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

        return ['data' => $data, 'sectorOne' => $sectorOne];
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

        $location = trim($request->location, " ");
        $metro = trim($request->metro, " ");
        $region = trim($request->region, " ");
        $industry = trim($request->industry, " ");
        $industry_two = trim($request->industry_two, " ");

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
            ->when($industry, function ($query, $industry) {
                $query->where('industry', $industry);
            })
            ->when($industry_two, function ($query, $industry_two) {
                $query->where('industry_two', $industry_two);
            })
            ->orderBy(DB::raw('ISNULL(location), location'), 'ASC')
            ->orderBy(DB::raw('ISNULL(metro), metro'), 'ASC')
            ->orderBy(DB::raw('ISNULL(region), region'), 'ASC')
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