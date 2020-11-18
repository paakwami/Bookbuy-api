<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class AgentPayment extends Model
{
    protected $fillable = [
        'user_id', 'agent_id','payment_method_id', 'amount','verified'
    ];

    public function Agent(){
        return $this->belongsTo('App\model\Agent');
    }
    public function User(){
        return $this->belongsTo('App\User');
    }
    public function PaymentMethod(){
        return $this->belongsTo('App\model\PaymentMethod');
    }
    public function transact()
    {
        return $this->morphOne('App\model\Transact', 'transactable');
    }
    public function AgentPaymentDisection(){
        return $this->hasMany('App\model\AgentPaymentDisection');
    }
    public function ReconTransact()
    {
        return $this->morphOne('App\model\ReconTransact', 'recon_transactable');
    }
}
