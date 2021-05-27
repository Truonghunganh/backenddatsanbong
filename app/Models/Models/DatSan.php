<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatSan extends Model
{
    use HasFactory;
    protected $fillable = [
        'idsan',
        'iduser',
        'start_time',
        'price',
        'xacnhan',
        'Create_time'    
    ];
    public $timestamps = false;
    protected $table = "datsans";
    public function San()
    {
        return $this->belongsTo('App\Models\Models\San', 'idsan', 'id');
    }
    public function User()
    {
        return $this->belongsTo('App\Models\Models\User', 'iduser', 'id');
    }
    
}
