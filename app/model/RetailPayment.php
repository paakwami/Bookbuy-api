<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class RetailPayment extends Model
{
    protected $fillable = [
        'agent_id', 'retail_id', 'payment_method_id', 'amount'
    ];

    public function Agent(){
        return $this->belongsTo('App\model\Agent');
    }
    public function Retail(){
        return $this->belongsTo('App\model\Retail');
    }
    public function PaymentMethod(){
        return $this->belongsTo('App\model\PaymentMethod');
    }
    public function RetailPaymentDisection(){
        return $this->hasMany('App\model\RetailPaymentDisection');
    }

}
