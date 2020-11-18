<?php

namespace App\Http\Controllers;

use App\model\AgentPaymentDisection;
use App\Helpers\Sms\Maker;
use App\Http\Resources\AgentPayment\AgentPaymentCollection;
use App\Http\Resources\AgentPayment\AgentPaymentResource;
use App\model\Agent;
use App\model\AgentDetail;
use App\model\AgentPayment;
use App\model\PaymentMethod;
use App\model\PublisherDetail;
use App\model\PublisherSaleInvoice;
use App\model\ReconTransact;
use App\model\Transact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $payments = Auth::User()->AgentPayment;
        return AgentPaymentCollection::collection($payments);
    }

    public function AgentPayments(Request $request)
    {
        $payments = Auth::User()->AgentPayment->where('agent_id', $request[0]);
        return AgentPaymentCollection::collection($payments);
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
            'amount'=>'required|integer',
            'agent_id'=>'required|integer',
            'payment_method_id'=>'required',
        ]);
        $user = Auth::user();
        $agent = Agent::find($request->get('agent_id'));
        $payment = PaymentMethod::find($request->get('payment_method_id'));

        $agentpayment = new AgentPayment([
            'amount'=> $request->get('amount'),
            'agent_id'=> $agent->id,
            'user_id' => $user->id,
            'payment_method_id' =>$payment->id,
            'verified' => 1,
        ]);
        $agentpayment->save();
        $amount = $agentpayment->amount;
        do{
        $PublisherSaleInvoice = PublisherSaleInvoice::orderBy('id', 'desc')->where([
            ['agent_id' ,'=', $agent->id],
            ['publisher_id' ,'=', $user->id],
            ['completed' ,'=', false]])
            ->get()->last();

            $am = ($PublisherSaleInvoice->after_discount - $PublisherSaleInvoice->amount_paid);

            if($am >= $amount){
                $paymentdisection = new AgentPaymentDisection([
                    'retail_payment_id'=> $agentpayment->id,
                    'agent_sale_invoice_id'=> $PublisherSaleInvoice->id,
                    'agent_payment_id'=>$agentpayment->id,
                    'publisher_sale_invoice_id'=>$PublisherSaleInvoice->id,
                    'amount' => $amount
                ]);
                $paymentdisection->save();

                $PublisherSaleInvoice->amount_paid = $PublisherSaleInvoice->amount_paid + $paymentdisection->amount;
                if($am == $amount){
                    $PublisherSaleInvoice->completed = true;
                }else{
                    $PublisherSaleInvoice->completed = false;
                }
                $PublisherSaleInvoice->save();
                $amount = $amount - $am;
            }else{
                $am = ($PublisherSaleInvoice->after_discount - $PublisherSaleInvoice->amount_paid);

                $paymentdisection = new AgentPaymentDisection([
                    'retail_payment_id'=> $agentpayment->id,
                    'agent_sale_invoice_id'=> $PublisherSaleInvoice->id,
                    'amount' => $am
                ]);
                $paymentdisection->save();
                $PublisherSaleInvoice->amount_paid = $PublisherSaleInvoice->amount_paid + $paymentdisection->amount;
                $PublisherSaleInvoice->completed = true;
                $PublisherSaleInvoice->save();

                $amount = $amount - $paymentdisection->amount;
            }

        }while ($amount > 0);
        $transact = Transact::orderBy('id', 'desc')->where([
            ['agent_id' ,'=', $agent->id],
            ['user_id' ,'=', $user->id]])
            ->get()->first();
        if($transact===null){
            $transact = new Transact(
                [
                    'agent_id' => $agent->id,
                    'user_id' => $user->id,
                    'pbalance' => 0,
                    'cbalance' => 0 + $agentpayment->amount,
                    'madeby' =>'publisher',
                    'amount'=>$agentpayment->amount,
                    'transacttype'=>'credit',
                    'transactable_id'=>$agentpayment->id,
                    'transactable_type'=>'App\model\AgentPayment'
                ]
            );
            $transact->save();
            $message = 'Dear '.$agent->name.', you have made a payment of  GHC '.$transact->amount.' to ' .$user->name.'. Your balance is GHC '.$transact->cbalance;
            Maker::sendSms('Bookbuy', $agent->phone, $message);
        }else{

            $tran = new Transact(
                [
                    'agent_id' => $agent->id,
                    'user_id' => $user->id,
                    'pbalance' => $transact->cbalance,
                    'cbalance' => $transact->cbalance + $agentpayment->amount,
                    'madeby' =>'publisher',
                    'amount'=>$agentpayment->amount,
                    'transacttype'=>'credit',
                    'transactable_id'=>$agentpayment->id,
                    'transactable_type'=>'App\model\AgentPayment'
                ]
            );
            $tran->save();
            $message = 'Dear '.$agent->name.', you have made a payment of  GHC '.$tran->amount.' to ' .$user->name.'. Your balance is GHC '.$tran->cbalance;
            Maker::sendSms('Bookbuy', $agent->phone, $message);
        }

        $agentdetails = AgentDetail::find($agent->id);
        $agentdetails->total_debt = $agentdetails->total_debt + $agentpayment->amount;
        $agentdetails->total_to_be_paid_to_publisher = $agentdetails->total_to_be_paid_to_publisher - $agentpayment->amount;
        $agentdetails->save();

        $publisherdetails = PublisherDetail::find($user->id);
        $publisherdetails->agents_balance = $publisherdetails->agents_balance + $agentpayment->amount;
        $publisherdetails->received_from_agents = $publisherdetails->received_from_agents + $agentpayment->amount;
        $publisherdetails->received_but_with_agents = $publisherdetails->received_but_with_agents - $agentpayment->amount;
        $publisherdetails->save();

        $reconciletransact = ReconTransact::orderBy('id', 'desc')->where([
            ['agent_id' ,'=', $agent->id],
            ['user_id' ,'=', $user->id]])
            ->get()->first();

        $rec = new ReconTransact(
            [
                'agent_id' => $agent->id,
                'user_id' => $user->id,
                'amount_supplied' => $reconciletransact->amount_supplied,
                'payment_received_by_agent' =>$reconciletransact->payment_received_by_agent,
                'in_debt'=>$reconciletransact->in_debt,
                'payment_to_publisher'=>$reconciletransact->payment_to_publisher,
                'in_debt_to_publisher' => $reconciletransact->in_debt_to_publisher - $agentpayment->amount,
                'transactiontype'=>'paypub',
                'recon_transactable_id'=>$agentpayment->id,
                'recon_transactable_type'=>'App\model\AgentPayment'
            ]
        );
        $rec->save();
        $message = [
            'userdetails'=>$user->PublisherDetail,
            'payment'=>new AgentPaymentResource($agentpayment),
        ];
        return response( $message, 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\model\AgentPayment  $agentPayment
     * @return \Illuminate\Http\Response
     */
    public function show(AgentPayment $agentPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\model\AgentPayment  $agentPayment
     * @return \Illuminate\Http\Response
     */
    public function edit(AgentPayment $agentPayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\model\AgentPayment  $agentPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AgentPayment $agentPayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\model\AgentPayment  $agentPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(AgentPayment $agentPayment)
    {
        //
    }
}
