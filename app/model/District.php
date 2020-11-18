<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = [
        'name','region_id'
    ];
    public function Region()
    {
        return $this->belongsTo('App\model\Region');
    }
}
