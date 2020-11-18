<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name', 'price', 'edition', 'user_id', 'book_image','learner_stage_id', 'status','series_id','sale'
    ];

    public function learner_stage()
    {
        return $this->belongsTo('App\model\LearnerStage');
    }

    public function publisherstock(){
        return $this->hasMany('App\model\PublisherStock');
    }
    public function itemstockpublisher(){
        return $this->hasOne('App\model\ItemStockPublisher');
    }
    public function ItemStockAgent(){
        return $this->belongsTo('App\model\ItemStockAgent');
    }
    public function Series()
    {
        return $this->belongsTo('App\model\Series');
    }
}
