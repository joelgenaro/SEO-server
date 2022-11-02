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
        $posts = DB::table('companies')
            ->orderBy(DB::raw('ISNULL(Location), Location'), 'ASC')
            ->orderBy(DB::raw('ISNULL(Metro), Metro'), 'ASC')
            ->orderBy(DB::raw('ISNULL(Region), Region'), 'ASC')
            ->orderBy(DB::raw('ISNULL(Locality), Locality'), 'ASC')
            ->orderBy(DB::raw('ISNULL(Industry), Industry'), 'ASC')
            ->orderBy(DB::raw('ISNULL("Industry 2"), "Industry 2"'), 'ASC')
            ->get();

        return $posts;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
