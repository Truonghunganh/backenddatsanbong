<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'iduser',
        'idquan',
        'review',
        'Review_time'
    ];
    public $timestamps = false;
    protected $table = "reviews";
    
}
