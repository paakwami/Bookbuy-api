<?php

namespace App\Http\Controllers;

use App\Helpers\Sms\Maker;
use App\Http\Resources\Payment\PaymentCollection;
use App\Http\Resources\PublisherServe\PublisherInitialize;
use App\Http\Resources\PublisherServe\PublisherServeResource;
use App\model\Agent;
use App\model\AgentStock;
use App\model\Batch;
use App\model\Item;
use App\model\ItemStockAgent;
use App\model\ItemStockPublisher;
use App\model\PublisherDetail;
use App\model\PublisherSaleDetail;
use App\model\PublisherSaleInvoice;
use App\model\AgentPayment;
use App\model\ReconTransact;
use App\model\Transact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;

class PublisherController extends Controller
{
    public function initialization(){
            //Auth::guard('api')->check();
            $publisher = Auth::guard('api')->user();
            return PublisherInitialize::make($publisher);
            //return response()->json($publisher->PublisherDetail, 200);


        //return $user = Auth::User();
        //return PublisherInitialize::make($user);
    }

    public function saleCalculator(Request $request){

        $items = $request->get('tickets');
        $sold = 0;

        foreach ($items as $i){
            $item_id = $i['item']['id'];
            $quantity = $i['quantity'];


            $item = Item::find($item_id);
            $itemstock = ItemStockPublisher::find($item_id);
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

    public function publisherServe(){
        $publisher = Auth::guard('api')->user();
        //return $publisher->publisherServe;
        return PublisherServeResource::collection($publisher->publisherServe);

    }

    public function createSale(Request $request)
    {
        $ag = $request->get('agent');
        $items = $request->get('tickets');
        $valuesold = 0;

        $user = Auth::user();
        $publisherdetail = $user->PublisherDetail;
        $agent = Agent::find($ag['id']);
        $agentdetail = $agent->AgentDetail;

        $userinvoices = PublisherSaleInvoice::where([
            ['agent_id' ,'=', $agent->id],
            ['publisher_id' ,'=', $user->id]])
            ->whereYear('created_at', '=', date('Y'))
            ->get()->count();

        $ag = strtoupper(mb_substr($agent->name, 0, 2));
        $pb = strtoupper(mb_substr($user->name, 0, 2));
        $userinvoices = $userinvoices + 1;
        $userinvoices = sprintf( '%03d', $userinvoices );
        $invoicenumber = date('Y').'-'.$userinvoices.'/'.$pb.'/'.$ag;

            $invoice = new PublisherSaleInvoice(
            [
                'discount' => $request->get('discount'),
                'agent_id' => $agent->id,
                'publisher_id' => $user->id,
                'invoicenumber' => $invoicenumber,
                'completed' => false,
                'amount_paid'=>0
            ]
        );

        $invoice->save();
        foreach ($items as $i){
            $item_id = $i['item']['id'];
            $quantity = $i['quantity'];


            $item = Item::find($item_id);
            $itemstock = ItemStockPublisher::find($item_id);

            $invoicedetails = new PublisherSaleDetail([
                'item_id' => $item->id,
                'quantity'=> $quantity,
                'publisher_sale_invoice_id'=> $invoice->id,
            ]);


            $invoicedetails->save();
            $valuesold = $valuesold + $item->price * $quantity;
            $itemstock->remaining_stock = $itemstock->remaining_stock - $quantity;
            $itemstock->save();

            $item = ItemStockAgent::where([
                ['item_id','=', $item->id],
                ['agent_id','=', $agent->id]])
                ->get();

           if(!$item->isEmpty()){
                $item = $item[0];
                   $item->remaining_stock = $item->remaining_stock + $quantity;
                   $item->save();
           }else{
               $item = new ItemStockAgent([
                   'remaining_stock'=> $quantity,
                   'item_id'=>$item_id,
                   'agent_id'=>$agent->id
               ]);
               $item->save();
           }

            $stocking = new AgentStock([
                'added' => $quantity,
                'item_id' => $item_id,
                'agent_id' => $agent->id
            ]);

            $stocking->save();

            $batch = new Batch([
                'added' => $quantity,
                'item_id' => $item_id,
                'discount' =>$request->get('discount'),
                'agent_id' => $agent->id
            ]);

            $batch->save();
        }

        $sale = $request->get('discount')/100;
        $sale = 1 - $sale;

        $sold = $valuesold * $sale;

        $invoice->after_discount = $sold;
        $invoice->total_sale = $valuesold;

        //updating ledger of publisher
        if ($publisherdetail === null){
            $publisherdetail = new PublisherDetail([
                'user_id'=>$user->id,
                'sold_to_agents'=>0,
                'agents_balance'=>0,
                'sold_by_agents'=>0,
                'received_from_agents'=>0,
                'received_but_with_agents'=>0,
                'indebt_with_retailers'=>0,
                'Year'=>date('Y')
            ]);
            $publisherdetail->save();
        }else{
            $publisherdetail->sold_to_agents = $publisherdetail->sold_to_agents + $sold;
            $publisherdetail->agents_balance = $publisherdetail->agents_balance - $sold;
            $publisherdetail->save();
        }

        $invoice->save();

        $transact = Transact::where([
            ['agent_id' ,'=', $agent->id],
            ['user_id' ,'=', $user->id]])
            ->get()->last();
        if($transact===null){
            $transact = new Transact(
                [
                    'agent_id' => $agent->id,
                    'user_id' => $user->id,
                    'pbalance' => 0,
                    'cbalance' => 0 - $invoice->after_discount,
                    'madeby' =>'publisher',
                    'amount'=>$invoice->after_discount,
                    'transacttype'=>'debit',
                    'transactable_id'=>$invoice->id,
                    'transactable_type'=>'App\model\PublisherSaleInvoice'
                ]
            );
            $transact->save();
            $message = 'Dear '.$agent->name.', you have purchased items worth GHC'.$transact->amount.' from ' .$user->name.'. Your balance is GHC '.$transact->cbalance;
            Maker::sendSms('Bookbuy', $agent->phone, $message);
        }else{

            $tran = new Transact(
                [
                    'agent_id' => $agent->id,
                    'user_id' => $user->id,
                    'pbalance' => $transact->cbalance,
                    'cbalance' => $transact->cbalance - $invoice->after_discount,
                    'madeby' =>'publisher',
                    'amount'=>$invoice->after_discount,
                    'transacttype'=>'debit',
                    'transactable_id'=>$invoice->id,
                    'transactable_type'=>'App\model\PublisherSaleInvoice'
                ]
            );
            $tran->save();

            $message = 'Dear '.$agent->name.', you have purchased items worth GHC'.$tran->amount.' from ' .$user->name.'. Your balance is GHC'.$tran->cbalance;
            Maker::sendSms('Bookbuy', $agent->phone, $message);
        }
        //updating ledger of agent
        $agentdetail->total_debt = $agentdetail->total_debt - $invoice->after_discount;
        $agentdetail->save();

        return response()->json($invoice, 200);

    }

    public function agentDetails(Request $request){
        $publisher = Auth::User();
        $agent = Agent::find($request->route('id'));
        $ledger = Transact::where([
            ['user_id' ,'=', $publisher->id],
            ['agent_id' ,'=', $agent->id]])
            ->get()->last();
        $invoices = PublisherSaleInvoice::where([
            ['publisher_id' ,'=', $publisher->id],
            ['agent_id' ,'=', $agent->id]])
            ->get();
        if(is_null($agent->AgentPayment)){
            $payments = ['data'=>[]];
        }else {
            $payments = PaymentCollection::collection(AgentPayment::where([
                ['user_id' ,'=', $publisher->id],
                ['agent_id' ,'=', $agent->id]])
                ->get());
        }
        $amount_due = ReconTransact::where([
            ['user_id', '=', $publisher->id],
            ['agent_id', '=', $agent->id]
        ])->get()->last();
        $message = [
            'ledger'=>$ledger,
            'invoices'=>$invoices,
            'payments'=>$payments,
            'amount_due' =>$amount_due
        ];
        return response()->json($message, 200);
    }
}
