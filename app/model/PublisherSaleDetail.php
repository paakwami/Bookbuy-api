<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class PublisherSaleDetail extends Model
{
    protected $fillable = [
        'item_id', 'quantity', 'publisher_sale_invoice_id'
    ];
}
