<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name'
    ];
    public function RetailPayment(){
        return $this->hasMany('App\model\RetailPayment');
    }
}
