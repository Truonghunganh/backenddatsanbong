<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
//use Illuminate\Foundation\Auth\User ;//as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Traits\StripePaymentBill;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject 
//class User extends Authenticatable 
{
//    use \Illuminate\Auth\Authenticatable;

   //  use StripePaymentBill;
    use Notifiable;
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    protected $guarded = [];
    protected $fillable = [
        "phone",
        "password",
    ];
    // protected $hidden = [
    //     "password",
    // ];
    protected $table = "users";
    public function San()
    {
        return $this->hasMany('App\Models\Models\DatSan', 'iduser', 'id');
    }
}
