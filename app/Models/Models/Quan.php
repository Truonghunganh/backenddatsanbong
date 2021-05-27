<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quan extends Model
{
    const ACTIVE_QUAN = 1;
    const INACTIVE_QUAN = 0;
    use HasFactory;
    protected $fillable = [
        'id',        
        'name',
        'image',
        'address',
        'phone'
           
    ];

    protected $table = "quans";
    public function San()
    {
        return $this->hasMany('App\Models\Models\San', 'idquan', 'id');
        // id : ở đây là id của quán
        // idquan : dùng chung cho  
    }
    
}
