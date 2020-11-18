<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class RetailOrderDetails extends Model
{
    protected $fillable = [
        'item_id', 'quantity' ,'retail_order_id'
          ];
    public function RetailOrder(){
        return $this->belongsTo('App\model\RetailOrder');
    }
    public function Item(){
        return $this->belongsTo('App\model\Item');
    }
}
