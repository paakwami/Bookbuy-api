<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class AgentStock extends Model
{
    protected $fillable = ['added', 'item_id', 'agent_id'];
}
