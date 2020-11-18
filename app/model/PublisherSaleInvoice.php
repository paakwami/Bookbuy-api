<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class PublisherSaleInvoice extends Model
{
    protected $fillable = [
        'total_sale', 'after_discount', 'discount','publisher_id', 'agent_id','invoicenumber','completed','amount_paid'
    ];

    public function User(){
        return $this->belongsTo('App\User');
    }
    public function Agent(){
        return $this->belongsTo('App\model\Agent');
    }
    public function transact()
    {
        return $this->morphOne('App\model\Transact', 'transactable');
    }
    public function AgentPaymentDisection(){
        return $this->hasMany('App\model\AgentPaymentDisection');
    }
}
