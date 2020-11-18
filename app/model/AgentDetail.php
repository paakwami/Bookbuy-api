<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class AgentDetail extends Model
{
    protected $fillable = [
        'agent_id','total_debt','total_sold','total_retail_debt','total_retail_payment','total_to_be_paid_to_publisher','Year'
    ];

    public function Agent(){
    return $this->belongsTo('App\model\Agent');
    }
}
