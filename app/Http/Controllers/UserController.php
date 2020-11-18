<?php

namespace App\Http\Controllers;

use App\Helpers\Sms\Maker;
use App\Http\Resources\Partner\PartnerCollection;
use App\Http\Resources\Partner\PartnerResource;
use App\Http\Resources\Partner\PartnerResouceA;
use App\model\Agent;
use App\model\PublisherDetail;
use App\model\UserRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    public function pendingUsers(){
        $users = User::where('verified','0')->get();
        return response()->json($users, 200);
    }
    public function validToken(){
        if(Auth::guard('api')->check()||Auth::guard('agent_api')->check()||Auth::guard('retail_api')->check()){
            return 'true';
        }
        return 'false';
    }

    public function partners(){
        if(Auth::guard('api')->check()){
            $user = Auth::guard('api')->user();
            $agents = $user->agent;
            return PartnerCollection::collection($agents);
//            return response($agents, 201);

        }
        if(Auth::guard('agent_api')->check()){
            $agent = Auth::guard('agent_api')->user();

//            return response($agent->User, 201);
            return PartnerCollection::collection($agent->user);

    }
    }
    public function approvedPartners(){
        if(Auth::guard('api')->check()){
            $user = Auth::guard('api')->user();
            $agents = $user->approvedagent;
            return PartnerCollection::collection($agents);
//            return response($agents, 201);

        }
        if(Auth::guard('agent_api')->check()){
            $agent = Auth::guard('agent_api')->user();

//            return response($agent->User, 201);
            return PartnerCollection::collection($agent->user);

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }
    public function userRequest(Request $request){


        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()){
            $val = ['validation_error' => $validator->errors()];
            return response()->json($val, 400);
        }

        if(Auth::guard('api')->check()){
            $publisher = Auth::guard('api')->user();
            $agent = Agent::find($request->get('id'));


            $req = DB::table('agent_user')
                ->where([['agent_id', $agent->id],['user_id', $publisher->id]])->first();


            if($req){
                if($req->by == 'publisher' && $req->status === 'pending') {
                    return response()->json('You have sent a request to agent pending approval', 400);
                }
                if($req->by == 'agent' && $req->status === 'pending') {
                    return response()->json('Agent have sent you a request already, please respond', 400);
                }
                if($req->status === 'approved') {
                    return response()->json('You are already in business with agent', 200);
                }
            }

            $publisher->Agent()->attach($agent);
            $update = [
                'by'     =>  'publisher',
                'status'   => 'pending',
            ];
            $result = DB::table('agent_user')
                ->where([['agent_id', $agent->id],['user_id', $publisher->id]])
                ->update($update);
            $message = 'Dear '.$agent->name.', '.$publisher->name. ' have asked you to sell his stock, please visit your dashboard to respond';

            Maker::sendSms('Bookbuy', $agent->phone, $message);

            $returnmessage = $agent->name.' have been notified.';
            $partner = new PartnerResource($publisher->agent->last());
            $mess = ['message'=> $returnmessage, 'partner'=>$partner];

            return response()->json($mess, 200);
        }

        if(Auth::guard('agent_api')->check()){
            $agent = Auth::guard('agent_api')->user();
            $publisher = user::find($request->get('id'));

            $req = DB::table('agent_user')
                ->where([['agent_id', $agent->id],['user_id', $publisher->id]])->first();

            if($req){
                if($req->by == 'agent' && $req->status === 'pending') {

                    return response()->json('You have sent a request to publisher pending approval', 400);
                }
                if($req->status === 'pending' && $req->by == 'publisher' ) {

                    return response()->json('Publisher have sent you a request already, please respond', 400);
                }
                if($req->status === 'approved') {
                    return response()->json('You are already in business with publisher', 400);
                }
            }

            $agent->User()->attach($publisher);
            $update = [
                'by'     =>  'agent',
                'status'   => 'pending',
            ];
            $result = DB::table('agent_user')
                ->where([['agent_id', $agent->id],['user_id', $publisher->id]])
                ->update($update);
            $message = 'Dear '.$publisher->name.', '.$agent->name. ' have applied to sell your products, please visit your dashboard to respond';

            Maker::sendSms('Bookbuy', $publisher->phone, $message);

            $returnmessage = $publisher->name.' have been notified.';
            $partner = new PartnerResource($agent->user->last());
            $mess = ['message'=> $returnmessage, 'partner'=>$partner];

            return response()->json($mess, 200);
        }

    }

    public function respondRequest(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if($validator->fails()){
            $val = ['validation_error' => $validator->errors()];
            return response()->json($val, 400);
        }

        if(Auth::guard('api')->check()){
            $publisher = Auth::guard('api')->user();

            $update = [
                'status'   => 'approved',
            ];
            $result = DB::table('agent_user')
                ->where([['agent_id', $request->get('id')],['user_id', $publisher->id]])
                ->update($update);

            return response()->json(new PartnerResource($publisher->agent->first()) , 200);
        }

        if(Auth::guard('agent_api')->check()){
            $agent = Auth::guard('agent_api')->user();

            $update = [
                'status'   => 'approved',
            ];
            $result = DB::table('agent_user')
                ->where([['agent_id', $agent->id],['user_id', $request->get('id')]])
                ->update($update);
            $user = User::find($request->get('id'));

            return response()->json(new PartnerResouceA($user), 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeUser(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'location' => 'required|string|max:255',
            'phone' => 'required|numeric|phone|unique:users|unique:agents',
        ]);

        if($validator->fails()){
            $val = ['validation_error' => $validator->errors()];
            return response()->json($val, 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'location' => $request->get('location'),
            'role'=>'publisher',
            'phone' => $request->get('phone'),
            'verified'=>false
        ]);

        $userdetails = PublisherDetail::create([
            'user_id'=>$user->id,
            'sold_to_agents'=>0,
            'agents_balance'=>0,
            'sold_by_agents'=>0,
            'received_from_agents'=>0,
            'received_but_with_agents'=>0,
            'indebt_with_retailers'=>0,
            'Year'=>date('Y')
        ]);

        $message = 'Dear '.$user->name.', You have succesfully registered in Ghana Book Store. Await approval Text Message';

        Maker::sendSms('Bookbuy', $user->phone, $message);
        return response()->json($user, 200);

    }

    public function fixUser(Request $request){
        $validator = Validator::make($request->all(), [
            'publisher_id' => 'required',
        ]);

        if($validator->fails()){
            $val = ['validation_error' => $validator->errors()];
            return response()->json($val, 400);
        }
        $user = User::find($request->get('publisher_id'));
        $user->verified = 1;
        $user->save();

        $role = config('roles.models.role')::where('name', '=', 'Publisher')->first();  //choose the default role upon user creation.
        $user->attachRole($role);

        $message = 'Dear '.$user->name.', Your Publisher Status Have been Approved, You can proceed to login';

        Maker::sendSms('Bookbuy', $user->phone, $message);
        return response()->json($user, 200);

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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return array
     */
    public function update(Request $request)
    {

        $token = Str::random(80);
        $credentials = $request->only('phone', 'password');

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user();
            if($user->verified == 0){
                return response()->json('Pending approval...please wait', 402);
            }elseif ($user->verified == 1){
                $request->user('web')->forceFill([
                    'api_token' => hash('sha256', $token),
                ])->save();

                $roles = $user->roles;
                $val = ['user' => $user, 'token'=> $token, 'roles'=>$roles, 'userdetails'=>$user->PublisherDetail];
                return response()->json($val, 200);
            }

        }elseif(Auth::guard('agents')->attempt($credentials))
        {
            $user = Auth::guard('agents')->user();
            $request->user('agents')->forceFill([
                'api_token' => hash('sha256', $token),
            ])->save();

            $val = ['user' => $user, 'token'=> $token,'userdetails'=>$user->AgentDetail];
            return response()->json($val, 200);

        }elseif(Auth::guard('retails')->attempt($credentials))
        {
            $user = Auth::guard('retails')->user();
            $request->user('retails')->forceFill([
                'api_token' => hash('sha256', $token),
            ])->save();

            $val = ['user' => $user, 'token'=> $token,'userdetails'=>$user];
            return response()->json($val, 200);
        }
            $val = ['credential_error' => 'Login details not correct, kindly recheck'];
            return response()->json($val, 404);
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
    public function userdetails()
    {
        //return response()->json(User::All(), 200);

        if(Auth::guard('api')->check()){
            $publisher = Auth::guard('api')->user();
            return response()->json($publisher->PublisherDetail, 200);
        }
        if(Auth::guard('agent_api')->check()){
            $agent = Auth::guard('agent_api')->user();
            $invoices = $agent->AgentSaleInvoice;
            $message = [
              'invoices'=> $invoices,
              'details'=>$agent->AgentDetail,
              'payments'=>$agent->RetailPayment
            ];
            return response()->json($message, 200);
        }
    }

   }
