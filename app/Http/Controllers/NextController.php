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
            ->orderBy(DB::raw('ISNULL(metro), metro'), 'ASC')
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
    public function getSearchOptions(Request $request)
    {
        //
        $type = null;
        $country = null;
        $city = null;
        $town = null;
        $locality = null;
        $data = null;
        $sectorOne = null;

        $type = trim($request->type, " ");
        $country = trim($request->country, " ");
        $city = trim($request->city, " ");
        $town = trim($request->town, " ");
        $locality = trim($request->locality, " ");
        $industry = trim($request->sectorOne, " ");

        switch ($type) {
            case 'country':
                $data = DB::table('companies')
                    ->select('region')
                    ->where('location_country', '=', $country)
                    ->whereNotNull('region')
                    ->orderBy('region', 'ASC')
                    ->distinct()
                    ->get();

                $sectorOne = DB::table('companies')
                    ->select('industry')
                    ->where('location_country', '=', $country)
                    ->whereNotNull('industry')
                    ->orderBy('industry', 'ASC')
                    ->distinct()
                    ->get();
                break;

            case 'city':
                $data = DB::table('companies')
                    ->select('metro')
                    ->where('location_country', '=', $country)
                    ->where('region', '=', $city)
                    ->whereNotNull('metro')
                    ->orderBy('metro', 'ASC')
                    ->distinct()
                    ->get();

                $sectorOne = DB::table('companies')
                    ->select('industry')
                    ->where('location_country', '=', $country)
                    ->where('region', '=', $city)
                    ->whereNotNull('industry')
                    ->orderBy('industry', 'ASC')
                    ->distinct()
                    ->get();
                break;

            case 'town':
                $data = DB::table('companies')
                    ->select('locality')
                    ->where('location_country', '=', $country)
                    ->where('region', '=', $city)
                    ->where('metro', '=', $town)
                    ->whereNotNull('locality')
                    ->orderBy('locality', 'ASC')
                    ->distinct()
                    ->get();

                $sectorOne = DB::table('companies')
                    ->select('industry')
                    ->where('location_country', '=', $country)
                    ->where('region', '=', $city)
                    ->where('metro', '=', $town)
                    ->whereNotNull('industry')
                    ->orderBy('industry', 'ASC')
                    ->distinct()
                    ->get();
                break;

            case 'locality':
                $sectorOne = DB::table('companies')
                    ->select('industry')
                    ->where('location_country', '=', $country)
                    ->where('region', '=', $city)
                    ->where('metro', '=', $town)
                    ->where('locality', '=', $locality)
                    ->whereNotNull('industry')
                    ->orderBy('industry', 'ASC')
                    ->distinct()
                    ->get();

            case 'sectorOne':
                $data = DB::table('companies')
                    ->select('industry_two')
                    ->when($country, function ($query, $country) {
                        $query->where('location_country', '=', $country);
                    })
                    ->when($city, function ($query, $city) {
                        $query->where('region', '=', $city);
                    })
                    ->when($town, function ($query, $town) {
                        $query->where('metro', '=', $town);
                    })
                    ->when($locality, function ($query, $locality) {
                        $query->where('locality', '=', $locality);
                    })
                    ->when($industry, function ($query, $industry) {
                        $query->where('industry', '=', $industry);
                    })
                    ->whereNotNull('industry_two')
                    ->orderBy('industry_two', 'ASC')
                    ->distinct()
                    ->get();

                break;

            default:
                # code...
                break;
        }

        return ['main' => $data, 'sectorOne' => $sectorOne];
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
        $country = null;
        $city = null;
        $town = null;
        $locality = null;
        $sectorOne = null;
        $sectorTwo = null;

        $country = trim($request->country, " ");
        $city = trim($request->city, " ");
        $town = trim($request->town, " ");
        $locality = trim($request->locality, " ");
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
                $query->where('metro', '=', $town);
            })
            ->when($locality, function ($query, $locality) {
                $query->where('locality', '=', $locality);
            })
            ->when($sectorOne, function ($query, $sectorOne) {
                $query->where('industry', '=', $sectorOne);
            })
            ->when($sectorTwo, function ($query, $sectorTwo) {
                $query->where('industry_two', '=', $sectorTwo);
            })
            ->orderBy(DB::raw('ISNULL(location_country), location_country'), 'ASC')
            ->orderBy(DB::raw('ISNULL(region), region'), 'ASC')
            ->orderBy(DB::raw('ISNULL(metro), metro'), 'ASC')
            ->orderBy(DB::raw('ISNULL(locality), locality'), 'ASC')
            ->paginate(10);

        return $data;
    }

    public function getDataWithText(Request $request)
    {

        $sector = null;
        $country = null;
        $sectors = ['industry', 'industry_two'];
        $countries = ['location_country', 'region', 'metro', 'locality'];
        $sector = trim($request->sector, " ");
        $country = trim($request->country, " ");

        $data = DB::table('companies')
            ->Where(function ($query) use ($sectors, $sector) {
                for ($i = 0; $i < count($sectors); $i++) {
                    $query->orwhere($sectors[$i], '=', $sector);
                }
            })
            ->Where(function ($query) use ($countries, $country) {
                for ($i = 0; $i < count($countries); $i++) {
                    $query->orwhere($countries[$i], '=', $country);
                }
            })
            ->orderBy(DB::raw('ISNULL(location_country), location_country'), 'ASC')
            ->orderBy(DB::raw('ISNULL(region), region'), 'ASC')
            ->orderBy(DB::raw('ISNULL(metro), metro'), 'ASC')
            ->orderBy(DB::raw('ISNULL(locality), locality'), 'ASC')
            ->paginate(10);

        return $data;

    }
}