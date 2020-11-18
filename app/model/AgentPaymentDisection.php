<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class AgentPaymentDisection extends Model
{
    protected $fillable = [
        'agent_payment_id','publisher_sale_invoice_id','amount'
    ];
    public function AgentPayment(){
        return $this->belongsTo('App\model\AgentPayment');
    }
    public function PublisherSaleInvoice(){
        return $this->belongsTo('App\model\PublisherSaleInvoice');
    }
}
