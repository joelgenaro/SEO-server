<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NextController extends Controller
{
    /**
     * Display a listing of the resource.
     *
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
            ->distinct()
            ->get();

        $sectorOne = DB::table('companies')
            ->select('industry')
            ->whereNotNull('industry')
            ->distinct()
            ->get();

        $sectorTwo = DB::table('companies')
            ->select('industry_two')
            ->whereNotNull('industry_two')
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

        foreach ($request->formData as $key => $value) {
            # code...
            switch ($value['name']) {
                case 'location':
                    $location = $value['value'];
                    break;
                case 'metro':
                    $metro = $value['value'];
                    break;
                case 'region':
                    $region = $value['value'];
                    break;
                case 'locality':
                    $locality = $value['value'];
                    break;
                case 'industry':
                    $industry = $value['value'];
                    break;
                case 'industry_two':
                    $industry_two = $value['value'];
                    break;
                case 'industry_three':
                    $industry_three = $value['value'];
                    break;

                default:
                    # code...
                    break;
            }
        }

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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
