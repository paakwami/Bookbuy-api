<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class AgentSaleInvoice extends Model
{
    protected $fillable = [
        'total_sale', 'after_discount', 'discount', 'agent_id', 'invoicenumber' , 'retail_id','amount_paid','completed'
    ];

    public function ReconInvoice(){
        return $this->hasMany('App\model\ReconInvoice');
    }

    public function AgentSaleDetail(){
        return $this->hasMany('App\model\AgentSaleDetail');
    }
    public function Agent(){
        return $this->belongsTo('App\model\Agent');
    }
    public function Retail(){
        return $this->belongsTo('App\model\Retail');
    }
    public function retail_transact()
    {
        return $this->morphOne('App\model\Retail_Transact', 'retail_transactable');
    }
    public function RetailPaymentDisection(){
        return $this->hasMany('App\model\RetailPaymentDisection');
    }
    public function ReconPayment(){
        return $this->hasMany('App\model\ReconPayment');
    }

}
