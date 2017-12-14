<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Merchant extends Authenticatable implements JWTSubject
{
    use Notifiable;
    //
    protected $fillable = [
        'name', 'password', 'phone','pid','type','imei'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getUserId();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getUserId()
    {

        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'phone'=>$this->phone,
            'type'=>'merchant',
            'merchant_type'=>$this->type,
            'pid'=>$this->pid,
            'imei'=>$this->imei
        ];//返回用户id
    }

}
