<?php

namespace App\Http\Controllers;

use App\model\Transact;
use Illuminate\Http\Request;

class TransactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       return Transact::with('transactable')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  \App\model\Transact  $transact
     * @return \Illuminate\Http\Response
     */
    public function show(Transact $transact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\model\Transact  $transact
     * @return \Illuminate\Http\Response
     */
    public function edit(Transact $transact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\model\Transact  $transact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transact $transact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\model\Transact  $transact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transact $transact)
    {
        //
    }
}
