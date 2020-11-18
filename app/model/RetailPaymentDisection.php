<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class RetailPaymentDisection extends Model
{
    protected $guarded = [];

    public function RetailPayment(){
        return $this->belongsTo('App\model\RetailPayment');
    }
    public function AgentSaleInvoice(){
        return $this->belongsTo('App\model\AgentSaleInvoice');
    }
    public function ReconPayment(){
        return $this->belongsTo('App\model\ReconPayment');
    }
    public function ReconTransact()
    {
        return $this->morphOne('App\model\ReconTransact', 'recon_transactable');
    }
}
