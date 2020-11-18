<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class ReconPayment extends Model
{
    protected $guarded = [];

    public function RetailPaymentDisection(){
        return $this->belongsTo('App\model\RetailPaymentDisection');
    }
    public function User(){
        return $this->belongsTo('App\User');
    }
    public function AgentSaleInvoice(){
        return $this->belongsTo('App\model\AgentSaleInvoice');
    }

}
