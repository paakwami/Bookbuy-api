<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class classgroup extends Model
{
    protected $fillable = [
        'name',
    ];


    public function LearnerStage()
    {
        return $this->belongsToMany('App\model\LearnerStage', 'classgroup_learner_stage');
    }
    public function Series()
    {
        return $this->hasMany('App\model\Series');
    }
}
