<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use jeremykenedy\LaravelRoles\Traits\HasRoleAndPermission;

class Agent extends Authenticatable
{
    use Notifiable;
    use HasRoleAndPermission;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','location','phone', 'link_token','role','total_debt','total_sold','total_retail_debt','total_retail_payment','total_to_be_paid_to_publisher'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function User(){
        return $this->belongsToMany('App\User')->withPivot('by', 'status');
    }

    public function AgentPayment(){
        return $this->hasMany('App\model\AgentPayment');
    }

    public function Retail(){
        return $this->belongsToMany('App\model\Retail','agent_retail');
    }
    public function AgentSaleInvoice(){
        return $this->hasMany('App\model\AgentSaleInvoice');
    }
    public function PublisherSaleInvoice(){
        return $this->hasMany('App\model\PublisherSaleInvoice');
    }
    public function RetailPayment(){
        return $this->hasMany('App\model\RetailPayment');
    }
    public function RetailTransact(){
        return $this->hasMany('App\model\RetailTransact');
    }
    public function ItemStockAgent(){
        return $this->hasMany('App\model\ItemStockAgent');
    }

    public function ReconTransact(){
        return $this->hasMany('App\model\ReconTransact');
    }
    public function AgentDetail(){
        return $this->hasOne('App\model\AgentDetail');
    }
}
