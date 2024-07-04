<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cartItem extends Model
{
    use HasFactory;
    protected $fillable = ['cart_id','product_varient_id','quantity','total'];

    public function carts(){
        return $this->belongsTo(Carts::class,'cart_id');
    }
    public function variants(){
        return $this->belongsTo(ProductVarient::class,'product_varient_id');
    }
}
