<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class PublisherServeDetail extends Model
{
    protected $fillable = [
        'quantity','item_id', 'publisher_serves_id'
    ];
    public function Item(){
        return $this->belongsTo('App\Model\Item');
    }
    public function PublisherServes(){
        return $this->belongsTo('App\model\PublisherServes');
    }

}
