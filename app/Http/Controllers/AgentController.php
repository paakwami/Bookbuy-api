<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemStock\ItemStockAgentCollection;
use App\model\AgentDetail;
use App\model\Batch;
use App\Helpers\Sms\Maker;
use App\model\Agent;
use App\model\AgentRequest;
use App\model\AgentSaleDetail;
use App\model\AgentSaleInvoice;
use App\model\item;
use App\model\ItemStockAgent;
use App\model\ReconcileInvoice;
use App\model\ReconcileTransact;
use App\model\ReconInvoice;
use App\model\ReconTransact;
use App\model\Retail;
use App\model\RetailTransact;
use App\model\ReconPublisherSale;
use App\model\ReconPublisherSaleDetail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AgentController extends Controller
{
public function saleCalculator(Request $request){

        $items = $request->get('tickets');
        $sold = 0;

        foreach ($items as $i){
            $item_id = $i['item']['item_id'];
            $quantity = $i['quantity'];


            $item = Item::find($item_id);
            $itemstock = ItemStockAgent::find($item_id);
            if($itemstock->remaining_stock < $quantity){
                return response()->json($item->name." stock below order", 401);
            }
            $valuesold = $item->price * $quantity;
            $sold = $sold + $valuesold;
        }
        $sale = $request->get('discount')/100;
        $sale = 1 - $sale;
        $sale = $sold * $sale;

        return response()->json('Order Approved', 200);

    }

public function createSale(Request $request)
    {
        //get initial values

        $re = $request->get('retailer');
        $items = $request->get('tickets');
        $discount = $request->get('discount');

        //created variables
        $valuesold = 0;

        //get the agents and his/her details, get the retailer as well
        $agent = Auth::user();
        $agentdetail = $agent->Agentdetail;
        $retailer = Retail::find($re['id']);

        //creating the invoice number
        $agentinvoices = AgentSaleInvoice::where([
            ['agent_id' ,'=', $agent->id],
            ['retail_id' ,'=', $retailer->id]])
            ->whereYear('created_at', '=', date('Y'))
            ->get()->count();

        $ag = strtoupper(mb_substr($agent->name, 0, 2));
        $re = strtoupper(mb_substr($retailer->name, 0, 2));
        $agentinvoices = $agentinvoices + 1;
        $agentinvoices = sprintf( '%03d', $agentinvoices );
        $invoicenumber = date('Y').'-'.$agentinvoices.'/'.$ag.'/'.$re;

        //storing initial invoice values
        $invoice = new AgentSaleInvoice(
            [
                'discount' => $request->get('discount'),
                'agent_id' => $agent->id,
                'retail_id' => $retailer->id,
                'invoicenumber' => $invoicenumber,
                'amount_paid' => 0,
                'completed' => false,
            ]
        );
        $invoice->save();
        $payallpublishers = 0;
        do{

            $pub = [];

            $first = array_slice($items, 0, 1)[0];
            $firstpublisher = Item::find($first['item']['item_id'])->series->user;


            $reconcileinvoice = new ReconInvoice([
                'agent_sale_invoice_id' => $invoice->id,
                'user_id' =>$firstpublisher->id,
            ]);

            $reconcileinvoice->save();

            $payagent = 0;
            $paypub = 0;

            foreach($items as $s){
                $now = Item::find($s['item']['item_id'])->series->user;
                if($now->id === $firstpublisher->id){
                    $key = array_search($s, $items);
                    array_splice($items, $key, 1);
                    array_push($pub, $s);
                }

                foreach ($pub as $i){
                    $item_id = $i['item']['item_id'];
                    $tobesold = $quantity = $i['quantity'];

                    $item = Item::find($item_id);
                    $itemstock = ItemStockAgent::find($item_id);
                    if($itemstock->remaining_stock <= 0 ||$itemstock->remaining_stock < $quantity){
                        return response()->json($item->name.' remaining stock cannot serve order', 200);
                    }

                    $invoicedetails = new AgentSaleDetail([
                        'item_id' => $item->id,
                        'quantity'=> $quantity,
                        'agent_sale_invoice_id'=> $invoice->id,
                    ]);

                    $valuediscount = 0;
                    $valueagent = 0;

                    do {
                        $batch = Batch::where('item_id', $item->id)->where('added', '>', '0')->first();
                        if ($batch->added < $tobesold) {
                            $tobesold = $tobesold - $batch->added;
                            $sold = $batch->added;
                            $total = $sold * $item->price;
                            $valuediscount = $valuediscount + $total * (1 - $batch->discount/100);
                            $valueagent = $valueagent + $total * (1 - $discount/100);
                            $valuesold = $valuesold+ $total;
                            $batch->added = $batch->added - $batch->added;
                            $batch->save();
                        }

                        $batch = Batch::where('item_id', $item->id)->where('added', '>', '0')->first();
                        if ($batch->added >= $tobesold){
                            $sold = $tobesold;
                            $total = $sold * $item->price;
                            $valuediscount = $valuediscount + $total * (1 - $batch->discount/100);
                            $valueagent = $valueagent + $total * (1 - $discount/100);
                            $valuesold = $valuesold + $total;
                            $batch->added = $batch->added - $tobesold;
                            $batch->save();
                            $tobesold = 0;
                        }



                    } while($tobesold >= 1);

                    $paypub = $paypub + $valuediscount;
                    $payagent = $payagent + $valueagent;
                    $payallpublishers = $payallpublishers + $paypub;

                    $invoicedetails->save();
                    $itemstock->remaining_stock = $itemstock->remaining_stock - $quantity;
                    $itemstock->save();

                    $pub=[];
                }
            }
            $reconcileinvoice->to_be_paid_to_publisher = $paypub;
            $reconcileinvoice->to_be_paid_to_agent = $payagent;
            $reconcileinvoice->save();

            $recconciletransacts = ReconTransact::orderBy('id', 'desc')->where([
                ['agent_id' ,'=', $agent->id],
                ['user_id' ,'=', $firstpublisher->id]])
                ->get()->first();

            if($recconciletransacts === null){
                $recconciletransacts = new ReconTransact(
                    [
                        'agent_id' => $agent->id,
                        'user_id' => $firstpublisher->id,
                        'amount_supplied' => $payagent,
                        'payment_received_by_agent' => 0,
                        'in_debt'=>$payagent,
                        'payment_to_publisher'=>$paypub,
                        'in_debt_to_publisher' => 0,
                        'transactiontype'=>'debit',
                        'recon_transactable_id'=>$reconcileinvoice->id,
                        'recon_transactable_type'=>'App\model\ReconInvoice'
                    ]
                );
                $recconciletransacts->save();
                $publisherdetail = User::find($recconciletransacts->user_id)->PublisherDetail;
                $publisherdetail->sold_by_agents = $publisherdetail->sold_by_agents + $paypub;
                $publisherdetail->indebt_with_retailers = $publisherdetail->indebt_with_retailers + $paypub;
                $publisherdetail->save();
            }else{

                $recconciletransacts2 = new ReconTransact(
                    [
                        'agent_id' => $agent->id,
                        'user_id' => $firstpublisher->id,
                        'amount_supplied' => $recconciletransacts->amount_supplied + $payagent,
                        'payment_received_by_agent' => $recconciletransacts->payment_received_by_agent,
                        'in_debt'=>$recconciletransacts->in_debt + $payagent,
                        'payment_to_publisher'=>$recconciletransacts->payment_to_publisher + $paypub,
                        'in_debt_to_publisher' => $recconciletransacts->in_debt_to_publisher,
                        'transactiontype'=>'debit',
                        'recon_transactable_id'=>$reconcileinvoice->id,
                        'recon_transactable_type'=>'App\model\ReconInvoice'
                    ]);
                 $recconciletransacts2->save();

                 $publisherdetail = User::find($recconciletransacts2->user_id)->PublisherDetail;
                 $publisherdetail->sold_by_agents = $publisherdetail->sold_by_agents + $paypub;
                 $publisherdetail->indebt_with_retailers = $publisherdetail->indebt_with_retailers + $paypub;
                 $publisherdetail->save();

            }

        }while($items != []);

        $invoice->total_sale = $valuesold;
        $invoice->after_discount = $valuesold * (1 - $invoice->discount/100);
        $invoice->save();

        $agentdetail->total_sold = $agentdetail->total_sold + $invoice->after_discount;
        $agentdetail->total_retail_debt = $agentdetail->total_retail_debt - $invoice->after_discount;
        $agentdetail->save();

        $transact = RetailTransact::orderBy('id', 'desc')->where([
            ['agent_id' ,'=', $agent->id],
            ['retail_id' ,'=', $retailer->id]])
            ->get()->last();

        if($transact===null){
            $transact = new RetailTransact(
                [
                    'agent_id' => $agent->id,
                    'retail_id' => $retailer->id,
                    'pbalance' => 0,
                    'cbalance' => 0 - $invoice->after_discount,
                    'madeby' =>'agent',
                    'amount'=>$invoice->after_discount,
                    'transacttype'=>'debit',
                    'retail_transactable_id'=>$invoice->id,
                    'retail_transactable_type'=>'App\model\AgentSaleInvoice'
                ]
            );
            $transact->save();
            $message = 'Dear '.$retailer->name.', you have purchased items worth GHC'.$transact->amount.' from ' .$agent->name.'. Your balance from '.$agent->name.' is GHC'.$transact->cbalance;
            Maker::sendSms('Bookbuy', $retailer->phone, $message);
        }else{

            $tran = new RetailTransact(
                [
                    'agent_id' => $agent->id,
                    'retail_id' => $retailer->id,
                    'pbalance' => $transact->cbalance,
                    'cbalance' => $transact->cbalance - $invoice->after_discount,
                    'madeby' =>'agent',
                    'amount'=>$invoice->after_discount,
                    'transacttype'=>'debit',
                    'retail_transactable_id'=>$invoice->id,
                    'retail_transactable_type'=>'App\model\AgentSaleInvoice'
                ]
            );
            $tran->save();
            $message = 'Dear '.$retailer->name.', you have purchased items worth GHC'.$tran->amount.' from ' .$agent->name.'. Your balance from '.$agent->name.' is GHC'.$tran->cbalance;
            Maker::sendSms('Bookbuy', $retailer->phone, $message);
        }
        $message = [
            'userdetails'=>$agentdetail,
            'invoice'=>$invoice,
        ];
        return response()->json($message, 200);

    }

public function stocks()
    {
        $agent = Auth::User();
        return ItemStockAgentCollection::collection($agent->itemstockagent);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
public function index()
    {
        $agent = Agent::all();
        return response()->json($agent, 200);
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:agents',
            'password' => 'required|string|min:6',
            'location' => 'required|string|max:255',
            'phone' => 'required|numeric|phone|unique:agents|unique:users',
        ]);

        if($validator->fails()){
            $val = ['validation_error' => $validator->errors()];
            return response()->json($val, 400);
        }

        $user = Agent::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'role'=>'agent',
            'location' => $request->get('location'),
            'phone' => $request->get('phone'),
        ]);
        $userdetails = AgentDetail::create([
            'agent_id'=>$user->id,
            'total_debt'=>0,
            'total_sold'=>0,
            'total_retail_debt'=>0,
            'total_retail_payment'=>0,
            'total_to_be_paid_to_publisher'=>0,
            'Year'=> date('Y')
        ]);

        $message = 'Dear '.$user->name.', You have Succesfully registered in Ghana Book Store, Proceed to Login';

        Maker::sendSms('bookbuy.shop', $user->phone, $message);
        return response()->json($user, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\model\Agent  $agent
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */

    public function show(Agent $agent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\model\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function edit(Agent $agent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\model\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Agent $agent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\model\Agent  $agent
     * @return \Illuminate\Http\Response
     */
    public function destroy(Agent $agent)
    {
        //
    }
}
