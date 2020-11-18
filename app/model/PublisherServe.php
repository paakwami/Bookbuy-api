<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class PublisherServe extends Model
{
    protected $fillable = [
        'user_id','retail_orders_id', 'sale', 'disbursed','served'
    ];
    public function User(){
        return $this->belongsTo('App\User');
    }
    public function retailOrder(){
        return $this->belongsTo('App\model\RetailOrder');
    }

}
