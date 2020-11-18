<?php

namespace App\Http\Controllers;

use App\Helpers\Sms\Maker;
use App\Http\Resources\Payment\PaymentCollection;
use App\Http\Resources\Retail\RetailCollection;
use App\Http\Resources\Retail\RetailResource;
use App\model\Retail;
use App\model\RetailTransact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Psy\Util\Str;

class RetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $agent = Auth::User();
        return RetailCollection::collection($agent->retail);
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

    public  function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'location' => 'required|string|max:255',
            'phone' => 'required|numeric|phone|unique:users|unique:agents|unique:retails',
        ]);

        if($validator->fails()){
            $val = ['validation_error' => $validator->errors()];
            return response()->json($val, 400);
        }


        $user = Retail::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'location' => $request->get('location'),
            'role'=>'retailer',
            'phone' => $request->get('phone'),
            'verified'=>false
        ]);

        $message = 'Dear '.$user->name.', You have successfully registered in BookBuy.shop. Proceed to login';

        Maker::sendSms('Bookbuy', $user->phone, $message);
        return response()->json($user, 200);
    }

    public function agentstore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'phone' => 'required|numeric|phone',
        ]);

        if($validator->fails()){
            $val = ['validation_error' => $validator->errors()];
            return response()->json($val, 400);
        }
        $agent = Auth::guard('agent_api')->user();

        $retail = Retail::where([
            ['phone','=', $request->get('phone')],
            ])
            ->get();
        if(!$retail->isEmpty()){
            return response()->json('You have already created a user with this phone number', 400);
        }
        $pass = str_random(6) ;
        $retail = Retail::create([
            'name' => $request->get('name'),
            'location' => $request->get('location'),
            'password' => Hash::make($pass),
            'phone' => $request->get('phone'),
            'role'=>'retailer',
            'verified'=>false
        ]);

        $retail->save();
        $agent->retail()->attach($retail);

        $message = 'Dear '.$retail->name.', You have succesfully being registered in BookBuy.shop, with password ' .$pass;

        Maker::sendSms('BookBuy', $retail->phone, $message);
        return response()->json( new RetailResource($retail), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\model\Retail  $retail
     * @return \Illuminate\Http\Response
     */
    public function show(Retail $retail)
    {
        dd($retail);
    }

    public function retailDetails(Request $request)
    {
        $agent = Auth::User();
        $retailer = Retail::find($request->route('id'));
        $ledger = RetailTransact::where([
            ['agent_id' ,'=', $agent->id],
            ['retail_id' ,'=', $request->route('id')]])
            ->get()->last();
        $invoices = $retailer->AgentSaleInvoice;
        $payments = PaymentCollection::collection($retailer->RetailPayment);
        $message = [
            'ledger'=>$ledger,
            'invoices'=>$invoices,
            'payments'=>$payments,
        ];
        return response()->json($message, 200);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\model\Retail  $retail
     * @return \Illuminate\Http\Response
     */
    public function edit(Retail $retail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\model\Retail  $retail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Retail $retail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\model\Retail  $retail
     * @return \Illuminate\Http\Response
     */
    public function destroy(Retail $retail)
    {
        //
    }
}
