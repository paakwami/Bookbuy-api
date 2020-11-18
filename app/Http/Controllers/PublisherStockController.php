<?php

namespace App\Http\Controllers;

use App\Http\Resources\Item\ItemResource;
use App\Http\Resources\Subject\SubjectResource;
use App\model\ItemStockPublisher;
use App\model\PublisherStock;
use App\model\subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublisherStockController extends Controller
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
        $request->validate([
            'item_id'=>'required',
            'added'=>'required|integer|min:1'
        ]);


            $publisherstock = new PublisherStock([
                'item_id' => $request->get('item_id'),
                'added'=> $request->get('added')
            ]);
            $publisherstock->save();


            $itemstock = $publisherstock->item->itemstockpublisher;
            $itemstock->remaining_stock = $itemstock->remaining_stock + $publisherstock->added;
            $itemstock->save();

        return response(new ItemResource($publisherstock->item), 201);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\model\PublisherStock  $publisherStock
     * @return \Illuminate\Http\Response
     */
    public function show(PublisherStock $publisherStock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\model\PublisherStock  $publisherStock
     * @return \Illuminate\Http\Response
     */
    public function edit(PublisherStock $publisherStock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\model\PublisherStock  $publisherStock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PublisherStock $publisherStock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\model\PublisherStock  $publisherStock
     * @return \Illuminate\Http\Response
     */
    public function destroy(PublisherStock $publisherStock)
    {
        //
    }
}
