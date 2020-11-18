<?php

namespace App\Http\Controllers;

use App\Http\Resources\Series\SeriesCollection;
use App\model\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SeriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $user = Auth::guard('api')->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'seriesorsingle' => 'required',
            'classgroup_id' => 'required',
            'subject_id' => 'required',
        ]);

        if($validator->fails()){
            $val = ['validation_error' => $validator->errors()];
            return response()->json($val, 400);
        }
        $series = Series::create([
            'name' => $request->get('name'),
            'seriesorsingle' => $request->get('seriesorsingle'),
            'classgroup_id' => $request->get('classgroup_id'),
            'location' => $request->get('location'),
            'user_id'=>$user->id,
            'subject_id' => $request->get('subject_id'),

        ]);
        return response()->json(SeriesCollection::make($series), 200);
    }

    public function publisherSeries(Request $request)
    {
        $user = Auth::guard('api')->user();
        return SeriesCollection::collection($user->series);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Series  $series
     * @return \Illuminate\Http\Response
     */
    public function show(Series $series)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Series  $series
     * @return \Illuminate\Http\Response
     */
    public function edit(Series $series)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Series  $series
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Series $series)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Series  $series
     * @return \Illuminate\Http\Response
     */
    public function destroy(Series $series)
    {
        //
    }
}
