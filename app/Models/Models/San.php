<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class San extends Model
{
    use HasFactory;
    protected $fillable = [
        'idquan',
        'name',
        'numberpeople',
        'trangthai',
        'priceperhour'
           
    ];

    protected $table = "sans";
    public function Quan()
    {
        return $this->belongsTo('App\Models\Models\Quan', 'idquan', 'id');
        
    }
    public function DatSan()
    {
        return $this->hasMany('App\Models\Models\DatSan', 'idsan', 'id');
     
    }
    

}
