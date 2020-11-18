<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Transact extends Model
{
    protected $guarded = [];

    public function transactable()
    {
        return $this->morphTo();
    }
}
