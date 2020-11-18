<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    protected $fillable = [
        'name','seriesorsingle','user_id','classgroup_id','subject_id'
    ];
    public function classgroup()
    {
        return $this->belongsTo('App\model\classgroup');
    }
    public function User()
    {
        return $this->belongsTo('App\User');
    }
    public function Subject()
    {
        return $this->belongsTo('App\model\Subject');
    }
    public function Item()
    {
        return $this->hasMany('App\model\Item');
    }
}
