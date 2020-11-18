<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Retail extends Authenticatable
{
    protected $fillable = [
        'name', 'location', 'email','password', 'key', 'phone','agent_id','role', 'link_token',
    ];

    public function Agent(){
        return $this->belongsToMany('App\model\Agent','agent_retail');
    }
    public function AgentSaleInvoice(){
        return $this->hasMany('App\model\AgentSaleInvoice');
    }
    public function RetailPayment(){
        return $this->hasMany('App\model\RetailPayment');
    }
    public function RetailTransact(){
        return $this->hasMany('App\model\RetailTransact');
    }
    public function retailOrder(){
        return $this->hasMany('App\model\RetailOrder');
    }
}
