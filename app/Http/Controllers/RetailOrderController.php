<?php

namespace App\Http\Controllers;

use App\Http\Resources\RetailOrder\RetailResource;
use App\model\Item;
use App\model\PublisherServe;
use App\model\PublisherServeDetail;
use App\model\RetailOrder;
use App\model\RetailOrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RetailOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::guard('retail_api')->user();
        return response()->json(RetailResource::collection($user->retailOrder), 201);
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
        $form = $request[0]['form'];
        $order = $request[0]['cart'];
        $user = Auth::guard('retail_api')->user();

        $retailOrder = new RetailOrder([
            'firstName' => $form['firstName'],
            'lastName' => $form['lastName'],
            'district' => $form['district'],
            'region' => $form['region']['id'],
            'address' => $form['address'],
            'city' => $form['city'],
            'gps' => $form['gps'],
            'phone' => $form['phone'],
            'sale'=>$form['total'],
            'retails_id'=>$user->id

        ]);
        $retailOrder->save();

        foreach ($order as $o){
            $product = $o['product'];
            $quantity = $o['data']['quantity'];
            $product = Item::find($product['id']);

            $retailOrderDetail = new RetailOrderDetails([
             'item_id' => $product->id,
             'quantity' => $quantity,
              'retail_order_id' => $retailOrder->id
            ]);
            $retailOrderDetail->save();


        }

        do{
            $pub = [];
            $pubsale = 0;

            $first = array_slice($order, 0, 1)[0];
            $firstpublisher = Item::find($first['product']['id'])->series->user;

           // return  $agent = Auth::guard('retail_api')->user();
           //check if agents of retailer have order if not push order to publisher;

            $publisherServe = new PublisherServe([
                'user_id'=>$firstpublisher->id,
                'retail_orders_id'=>$retailOrder->id,
                'sale'=>0,
                'disbursed'=>false,
                'served'=>false,
            ]);
            $publisherServe->save();

            foreach($order as $s){
                $item = Item::find($s['product']['id']);
                if($item->series->user->id === $firstpublisher->id){
                    $key = array_search($s, $order);
                    array_splice($order, $key, 1);
                    array_push($pub, $s);

                    $publisherServeDetail = new PublisherServeDetail([
                        'quantity'=>$s['data']['quantity'],
                        'item_id'=>$item->id,
                        'publisher_serves_id'=>$publisherServe->id,
                    ]);
                    $publisherServeDetail->save();

                    $pubsale += $item->sale*$s['data']['quantity'];
                }
            }
            $publisherServe->sale = $pubsale;
            $publisherServe->save();

        }while($order != []);

        return response()->json($retailOrder, 201);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\model\RetailOrder  $retailOrder
     * @return \Illuminate\Http\Response
     */
    public function show(RetailOrder $retailOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\model\RetailOrder  $retailOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(RetailOrder $retailOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\model\RetailOrder  $retailOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RetailOrder $retailOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\model\RetailOrder  $retailOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(RetailOrder $retailOrder)
    {
        //
    }
}
