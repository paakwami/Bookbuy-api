<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class ReconTransact extends Model
{
    protected $guarded = [];

    public function User(){
        return $this->belongsTo('App\User');
    }
    public function Agent(){
        return $this->belongsTo('App\model\Agent');
    }

    public function recon_transactable()
    {
        return $this->morphTo();
    }
}
