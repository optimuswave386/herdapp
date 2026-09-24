<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class shoppingcart extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'unit_price',
        'quantity',
        'total_price',
    ];
    
    protected $hidden = [
        'remember_token',
    ];

}
