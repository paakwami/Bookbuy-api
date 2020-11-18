<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class PublisherDetail extends Model
{
    protected $fillable = [
        'user_id','sold_to_agents','agents_balance','sold_by_agents','received_from_agents','received_but_with_agents','indebt_with_retailers','Year'
    ];

    public function User(){
        return $this->belongsTo('App\User');
    }

}
