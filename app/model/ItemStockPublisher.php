<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class ItemStockPublisher extends Model
{
    protected $primaryKey = 'item_id';
    protected $fillable = [
        'remaining_stock', 'item_id'
    ];
    public function Item(){
        return $this->belongsTo('App\model\Item');
    }
}
