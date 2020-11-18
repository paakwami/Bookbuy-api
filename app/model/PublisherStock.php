<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class PublisherStock extends Model
{
    protected $fillable = [
        'added', 'item_id'
    ];
    public function Item(){
        return $this->belongsTo('App\model\Item');
    }

}
