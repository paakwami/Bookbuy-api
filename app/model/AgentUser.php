<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class AgentUser extends Model
{
    protected $fillable = [
        'by', 'status'
    ];
}
