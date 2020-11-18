<?php

namespace App\Http\Controllers;

use App\Helpers\Sms\Maker;
use App\Http\Resources\AgentPayment\AgentPaymentResource;
use App\Http\Resources\Payment\PaymentCollection;
use App\model\AgentSaleInvoice;
use App\model\PaymentMethod;
use App\model\ReconInvoice;
use App\model\ReconPayment;
use App\model\ReconTransact;
use App\model\Retail;
use App\model\RetailPayment;
use App\model\RetailPaymentDisection;
use App\model\RetailTransact;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RetailPaymentController extends Controller
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
//        $request->validate([
//            'amount'=>'required|integer',
//            'retail_id'=>'required|integer',
//            'payment_method_id'=>'required',
//        ]);

        $agent = Auth::User();
        $agentdetail = $agent->AgentDetail;
        $retailer = Retail::find($request->get('retail_id'));
        $payment = PaymentMethod::find($request->get('payment_method_id'));


        $retailpayment = new RetailPayment([
            'amount'=> $request->get('amount'),
            'agent_id'=> $agent->id,
            'retail_id' => $retailer->id,
            'payment_method_id' =>$payment->id,
            'verified' => 1,
        ]);
        $retailpayment->save();
        $amount = $request->get('amount');
        $payingpublishers = 0;
        do
        {
            $AgentSaleInvoice = AgentSaleInvoice::orderBy('id', 'desc')->where([
                ['agent_id' ,'=', $agent->id],
                ['retail_id' ,'=', $retailer->id],
                ['completed' ,'=', false]])
                ->get()->last();
            $am = ($AgentSaleInvoice->after_discount - $AgentSaleInvoice->amount_paid);


            if($am >= $amount)
            {
                $paymentdisection = new RetailPaymentDisection([
                    'retail_payment_id'=> $retailpayment->id,
                    'agent_sale_invoice_id'=> $AgentSaleInvoice->id,
                    'amount' => $amount
                ]);

                $paymentdisection->save();


                $AgentSaleInvoice->amount_paid = $AgentSaleInvoice->amount_paid + $paymentdisection->amount;
                    if($am == $amount){
                        $AgentSaleInvoice->completed = true;
                    }else{
                        $AgentSaleInvoice->completed = false;
                    }
                $AgentSaleInvoice->save();
                $amount = $amount - $am;
                $settles = $AgentSaleInvoice->ReconInvoice;
                foreach ($settles as $settle)
                {

                    $reconpayment = new ReconPayment([
                        'user_id' => $settle->user_id,
                        'retail_payment_disection_id' => $paymentdisection->id,
                        'agent_sale_invoice_id' => $AgentSaleInvoice->id,
                        'paid_to_agent' => $paymentdisection->amount,
                    ]);
                    $reconpayment->save();


                    $recconciletransacts = ReconTransact::orderBy('id', 'desc')->where([
                        ['agent_id' ,'=', $agent->id],
                        ['user_id' ,'=', $settle->user_id]])
                        ->get()->first();

                    $f = $paymentdisection->amount * ($settle->to_be_paid_to_publisher / $AgentSaleInvoice->after_discount);
                    $f = number_format($f, 2, '.', '');
                    $e = number_format($paymentdisection->amount * ($settle->to_be_paid_to_agent / $AgentSaleInvoice->after_discount), 2, '.', '');
                        $recconciletransacts = new ReconTransact(
                            [
                                'agent_id' => $agent->id,
                                'user_id' => $settle->user_id,
                                'amount_supplied' => $recconciletransacts->amount_supplied,
                                'payment_received_by_agent' =>$recconciletransacts->payment_received_by_agent + $paymentdisection->amount,
                                'in_debt'=>$recconciletransacts->in_debt - $e,
                                'payment_to_publisher'=>$recconciletransacts->payment_to_publisher - $f,
                                'in_debt_to_publisher' => $recconciletransacts->in_debt_to_publisher + $f,
                                'transactiontype'=>'credit',
                                'recon_transactable_id'=>$paymentdisection->id,
                                'recon_transactable_type'=>'App\model\PaymentDisection'
                            ]
                        );
                        $recconciletransacts->save();

                    $publsherdetails = User::find($recconciletransacts->user_id)->PublisherDetail;
                    $publsherdetails->received_but_with_agents = $publsherdetails->received_but_with_agents + $f;
                    $publsherdetails->indebt_with_retailers = $publsherdetails->indebt_with_retailers - $f;
                    $publsherdetails->save();

                        $payingpublishers = $payingpublishers + $f;
                }
            } else {
                $am = ($AgentSaleInvoice->after_discount - $AgentSaleInvoice->amount_paid);

                $paymentdisection = new RetailPaymentDisection([
                    'retail_payment_id' => $retailpayment->id,
                    'agent_sale_invoice_id' => $AgentSaleInvoice->id,
                    'amount' => $am
                ]);
                $paymentdisection->save();
                $AgentSaleInvoice->amount_paid = $AgentSaleInvoice->amount_paid + $paymentdisection->amount;
                $AgentSaleInvoice->completed = true;
                $AgentSaleInvoice->save();

                $amount = $amount - $paymentdisection->amount;

                $settles = $AgentSaleInvoice->ReconInvoice;
                foreach ($settles as $settle)
                {

                    $reconpayment = new ReconPayment([
                        'user_id' => $settle->user_id,
                        'retail_payment_disection_id' => $paymentdisection->id,
                        'agent_sale_invoice_id' => $AgentSaleInvoice->id,
                        'paid_to_agent' => $paymentdisection->amount,
                    ]);
                    $reconpayment->save();


                    $recconciletransacts = ReconTransact::orderBy('id', 'desc')->where([
                        ['agent_id' ,'=', $agent->id],
                        ['user_id' ,'=', $settle->user_id]])
                        ->get()->first();

                    $f = $paymentdisection->amount * ($settle->to_be_paid_to_publisher / $AgentSaleInvoice->after_discount);
                    $f = number_format($f, 2, '.', '');
                    $e = number_format($paymentdisection->amount * ($settle->to_be_paid_to_agent / $AgentSaleInvoice->after_discount),  2, '.', '');
                    $recconciletransacts = new ReconTransact(
                        [
                            'agent_id' => $agent->id,
                            'user_id' => $settle->user_id,
                            'amount_supplied' => $recconciletransacts->amount_supplied,
                            'payment_received_by_agent' =>$recconciletransacts->payment_received_by_agent + $paymentdisection->amount,
                            'in_debt'=>$recconciletransacts->in_debt - $e,
                            'payment_to_publisher'=>$recconciletransacts->payment_to_publisher - $f,
                            'in_debt_to_publisher' => $recconciletransacts->in_debt_to_publisher + $f,
                            'transactiontype'=>'credit',
                            'recon_transactable_id'=>$paymentdisection->id,
                            'recon_transactable_type'=>'App\model\PaymentDisection'
                        ]
                    );
                    $recconciletransacts->save();

                    $publsherdetails = User::find($recconciletransacts->user_id)->PublisherDetail;
                    $publsherdetails->received_but_with_agents = $publsherdetails->received_but_with_agents + $f;
                    $publsherdetails->indebt_with_retailers = $publsherdetails->indebt_with_retailers - $f;
                    $publsherdetails->save();

                    $payingpublishers = $payingpublishers + $f;
                }
                }
            }
                while ($amount > 0);


                    $transact = RetailTransact::where([
                        ['agent_id', '=', $agent->id],
                        ['retail_id', '=', $retailer->id]])
                        ->get()->last();
                if ($transact === null) {
                    $transact = new RetailTransact(
                        [
                            'agent_id' => $agent->id,
                            'retail_id' => $retailer->id,
                            'pbalance' => 0,
                            'cbalance' => 0 + $retailpayment->amount,
                            'madeby' => 'publisher',
                            'amount' => $retailpayment->amount,
                            'transacttype' => 'credit',
                            'retail_transactable_id' => $retailpayment->id,
                            'retail_transactable_type' => 'App\model\RetailPayment'
                        ]
                    );
                    $transact->save();
                    $message = 'Dear ' . $retailer->name . ', you have made a payment of  GHC ' . $transact->amount . ' to ' . $agent->name . '. Your balance is GHC ' . $transact->cbalance;
                     Maker::sendSms('Bookbuy', $retailer->phone, $message);

                } else {
                    $tran = new RetailTransact(
                        [
                            'agent_id' => $agent->id,
                            'retail_id' => $retailer->id,
                            'pbalance' => $transact->cbalance,
                            'cbalance' => $transact->cbalance + $retailpayment->amount,
                            'madeby' => 'agent',
                            'amount' => $retailpayment->amount,
                            'transacttype' => 'credit',
                            'retail_transactable_id' => $retailpayment->id,
                            'retail_transactable_type' => 'App\model\RetailPayment'
                        ]
                    );
                    $tran->save();
                    $message = 'Dear ' . $retailer->name . ', you have made a payment of  GHC ' . $tran->amount . ' to ' . $agent->name . '. Your balance is GHC ' . $tran->cbalance;
                    Maker::sendSms('Bookbuy', $retailer->phone, $message);
                }
        $agentdetail->total_retail_debt =$agentdetail->total_retail_debt + $retailpayment->amount;
        $agentdetail->total_retail_payment =$agentdetail->total_retail_payment + $retailpayment->amount;
        $agentdetail->total_to_be_paid_to_publisher = $agentdetail->total_to_be_paid_to_publisher + $payingpublishers;
        $agentdetail->save();

        $message = [
            'userdetails'=>$agentdetail,
            'payment'=>new PaymentCollection($retailpayment),
        ];
        return response($message, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\model\RetailPayment  $retailPayment
     * @return \Illuminate\Http\Response
     */
    public function show(RetailPayment $retailPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\model\RetailPayment  $retailPayment
     * @return \Illuminate\Http\Response
     */
    public function edit(RetailPayment $retailPayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\model\RetailPayment  $retailPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RetailPayment $retailPayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\model\RetailPayment  $retailPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(RetailPayment $retailPayment)
    {
        //
    }
}
