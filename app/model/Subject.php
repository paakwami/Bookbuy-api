<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name'
    ];

    public function Item(){
        return $this->hasManyThrough('App\model\Item', 'App\model\Series');
    }
    public function Series(){
        return $this->hasOne('App\model\Series');
    }
}
