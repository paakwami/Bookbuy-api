<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $fillable = ['added', 'item_id', 'agent_id', 'discount'];
}
