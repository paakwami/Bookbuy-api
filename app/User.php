<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use jeremykenedy\LaravelRoles\Traits\HasRoleAndPermission;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoleAndPermission;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','location','phone','verified','role'
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

    public function Item()
    {
        return $this->hasManyThrough('App\model\Item', 'App\model\Series');
    }
    public function Agent(){
        return $this->belongsToMany('App\model\Agent')->withPivot('by', 'status');
    }

    public function ApprovedAgent(){
        return $this->belongsToMany('App\model\Agent')->withPivot('by', 'status')->where('status','approved');
    }

    public function AgentPayment(){
        return $this->hasMany('App\model\AgentPayment');
    }
    public function ReconInvoice(){
        return $this->hasMany('App\model\ReconInvoice');
    }
    public function ReconPayment(){
        return $this->hasMany('App\model\ReconPayment');
    }
    public function ReconTransact(){
        return $this->hasMany('App\model\ReconTransact');
    }
    public function PublisherDetail(){
        return $this->hasOne('App\model\PublisherDetail');
    }
    public function Series(){
        return $this->hasMany('App\model\Series');
    }
    public function publisherServe(){
        return $this->hasMany('App\model\PublisherServe');
    }
}
