<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class ReconInvoice extends Model
{
    protected $fillable = [
        'agent_sale_invoice_id', 'user_id','to_be_paid_to_publisher'
    ];

    public function AgentSaleInvoice(){
        return $this->belongsTo('App\model\AgentSaleInvoice');
    }
    public function User(){
        return $this->belongsTo('App\User');
    }
    public function ReconTransact()
    {
        return $this->morphOne('App\model\ReconTransact', 'recon_transactable');
    }
}
