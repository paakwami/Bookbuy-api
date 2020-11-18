<?php

namespace App\Http\Controllers;

use App\Http\Resources\Item\ItemCollection;
use App\Http\Resources\Item\ItemResource;
use App\model\ItemStockPublisher;
use App\model\Item;
use App\model\LearnerStage;
use App\model\PublisherStock;
use App\model\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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

    public function publisherItem(Request $request)
    {
      $item = Auth::user()->Item;

        return ItemCollection::collection($item) ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $pics = '';
        $request->validate([
            'name'=>'required|string|max:255',
            'price'=>'required|integer',
            'sale'=>'required|integer',
            'edition'=>'required|integer',
            'learnerstage_id'=>'required',
            'stock'=>'required|integer|min:1'
        ]);
        $series = Series::where([
            ['user_id', '=' ,$user->id],
            ['id','=',$request->get('series_id')]
        ])->first();

        $item = item::where([
            ['learner_stage_id','=', $request->get('learnerstage_id')],
            ['price','=', $request->get('price')],
            ['price','=', $request->get('sale')],
            ['series_id','=', $series->id],
            ['edition' ,'=', $request->get('edition')]])
            ->get();
        if(!$item->isEmpty()){
            return response('Item already Exist', 302);
        }
        $learnerstage = LearnerStage::find($request->get('learnerstage_id'));

        if($request->hasFile('image')){
            $file      = $request->file('image');
            $filename  = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $picture   = $request->get('name').'-'.$learnerstage->name.'.'.$extension;

            //move image to public/img folder
            $file->move(public_path('img/'. $user->name.'/'.$series->name), $picture);

            $pics = '/img/'.$user->name.'/'.$series->name.'/'.$picture;
            //return $request->root().'/api/img/'.$user->name.'/'.$picture;
           // return response()->json(["message" => "Image Uploaded Succesfully"]);
        }
        else
        {
            $pics = '';
        }

        $item = new Item([
            'name' => $request->get('name'),
            'price' => $request->get('price'),
            'sale' => $request->get('sale'),
            'edition' => $request->get('edition'),
            'learner_stage_id' => $request->get('learnerstage_id'),
            'status' => 1,
            'series_id'=>$series->id,
            'book_image'=> $pics
        ]);
        $item->save();

            $publisherstock = new PublisherStock([
                    'item_id'=>$item->id,
                    'added'=>$request->get('stock')
                ]

            );
            $publisherstock->save();
            $itemstockpublisher = new ItemStockPublisher([
                'remaining_stock' => $publisherstock->added,
                'item_id'=>$item->id,
            ]);
            $itemstockpublisher->save();
            return response()->json(new ItemResource($item), 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\model\item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\model\item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\model\item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, item $item)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\model\item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(item $item)
    {
        //
    }
}
