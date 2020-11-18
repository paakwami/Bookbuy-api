<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class LearnerStage extends Model
{
    protected $fillable = [
        'name'
    ];

    public function Item(){
        return $this->belongsTo('App\model\Item');
    }
    public function ClassGroup(){
        return $this->belongsToMany('App\model\classgroup', 'classgroup_learner_stage');
    }
}
