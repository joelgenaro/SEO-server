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
            ->orderBy(DB::raw('ISNULL(location), location'), 'ASC')
            ->orderBy(DB::raw('ISNULL(region), region'), 'ASC')
            ->orderBy(DB::raw('ISNULL(metro), metro'), 'ASC')
            ->orderBy(DB::raw('ISNULL(locality), locality'), 'ASC')
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
    public function getSearchOptions(Request $request)
    {
        //
        $type = null;
        $country = null;
        $city = null;
        $town = null;
        $locality = null;
        $data = null;

        $type = trim($request->type, " ");
        $country = trim($request->country, " ");
        $city = trim($request->city, " ");
        $town = trim($request->town, " ");
        $locality = trim($request->locality, " ");

        switch ($type) {
            case 'country':
                $data = DB::table('companies')
                    ->select('region')
                    ->where('location', '=', $country)
                    ->whereNotNull('region')
                    ->orderBy('region', 'ASC')
                    ->distinct()
                    ->get();

                break;

            case 'city':
                $data = DB::table('companies')
                    ->select('metro')
                    ->where('location', '=', $country)
                    ->where('region', '=', $city)
                    ->whereNotNull('metro')
                    ->orderBy('metro', 'ASC')
                    ->distinct()
                    ->get();
                break;

            case 'town':
                $data = DB::table('companies')
                    ->select('locality')
                    ->where('location', '=', $country)
                    ->where('region', '=', $city)
                    ->where('metro', '=', $town)
                    ->whereNotNull('locality')
                    ->orderBy('locality', 'ASC')
                    ->distinct()
                    ->get();
                break;

            case 'locality':
                $data = DB::table('companies')
                    ->select('industry', 'industry_two')
                    ->where('location', '=', $country)
                    ->where('region', '=', $city)
                    ->where('metro', '=', $town)
                    ->where('locality', '=', $locality)
                    ->orderBy('industry', 'ASC')
                    ->distinct()
                    ->get();
                break;

            default:
                # code...
                break;
        }

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
        $country = null;
        $city = null;
        $town = null;
        $locality = null;
        $sector = null;
        $sectorOne = null;
        $sectorTwo = null;

        $country = trim($request->country, " ");
        $city = trim($request->city, " ");
        $town = trim($request->town, " ");
        $locality = trim($request->locality, " ");
        $sector = trim($request->sector, " ");

        if ($sector) {
            # code...
            $sector = explode("-", $sector);
            $sectorOne = $sector[0] != 'null' ? $sector[0] : null;
            $sectorTwo = $sector[1] != 'null' ? $sector[1] : null;
        }

        $data = DB::table('companies')
            ->when($country, function ($query, $country) {
                $query->where('location', '=', $country);
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
            ->orderBy(DB::raw('ISNULL(location), location'), 'ASC')
            ->orderBy(DB::raw('ISNULL(region), region'), 'ASC')
            ->orderBy(DB::raw('ISNULL(metro), metro'), 'ASC')
            ->orderBy(DB::raw('ISNULL(locality), locality'), 'ASC')
            ->paginate(10);

        return $data;
    }

    public function getDataWithText(Request $request)
    {

        $search = null;
        $search = $request->search;

        $data = DB::table('companies')
            ->Where('location', 'like', '%' . $search . '%')
            ->orWhere('region', 'like', '%' . $search . '%')
            ->orWhere('metro', 'like', '%' . $search . '%')
            ->orWhere('locality', 'like', '%' . $search . '%')
            ->orderBy(DB::raw('ISNULL(location), location'), 'ASC')
            ->orderBy(DB::raw('ISNULL(region), region'), 'ASC')
            ->orderBy(DB::raw('ISNULL(metro), metro'), 'ASC')
            ->orderBy(DB::raw('ISNULL(locality), locality'), 'ASC')
            ->paginate(10);

        return $data;

    }
}