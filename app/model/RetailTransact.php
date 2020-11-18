<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class RetailTransact extends Model
{
    protected $fillable = [
        'agent_id', 'retail_id', 'pbalance', 'cbalance', 'amount', 'transacttype', 'retail_transactable_id', 'retail_transactable_type'
    ];

    public function Agent(){
        return $this->belongsTo('App\model\Agent');
    }
    public function Retail(){
        return $this->belongsTo('App\model\Retail');
    }
    public function retail_transactable()
    {
        return $this->morphTo();
    }
}
