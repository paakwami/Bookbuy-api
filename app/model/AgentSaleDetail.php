<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class AgentSaleDetail extends Model
{
    protected $fillable = [
        'item_id', 'quantity', 'agent_sale_invoice_id'
    ];

    public function AgentSaleInvoice(){
        return $this->belongsTo('App\model\AgentSaleInvoice');
    }
}
