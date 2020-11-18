<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class ItemStockAgent extends Model
{
    protected $primaryKey = 'item_id';
    protected $fillable = ['item_id', 'remaining_stock','agent_id'];

    public function Agent(){
        return $this->belongsTo('App\model\Agent');
    }
    public function Item(){
        return $this->belongsTo('App\model\Item');
    }
}
