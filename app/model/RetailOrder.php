<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class RetailOrder extends Model
{
    protected $fillable = [
        'firstName', 'lastName', 'district', 'region', 'address' , 'city' , 'gps' , 'phone' , 'sale' , 'retails_id'
    ];
    public function RetailOrderDetail(){
        return $this->hasMany('App\model\RetailOrderDetail');
    }
    public function Retail(){
        return $this->belongsTo('App\model\Retail');
    }
    public function PublisherServe(){
        return $this->hasMany('App\model\PublisherServe');
    }
}
