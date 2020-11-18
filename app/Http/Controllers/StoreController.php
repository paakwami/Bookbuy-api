<?php

namespace App\Http\Controllers;

use App\Http\Resources\Store\AllItems;
use App\User;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function getAllProducts()
    {
        //return User::All();
        return AllItems::collection(User::All());
    }
}
