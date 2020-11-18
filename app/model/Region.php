<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = [
        'name'
    ];

    public function District(){
        return $this->hasMany('App\model\District');
    }
}
