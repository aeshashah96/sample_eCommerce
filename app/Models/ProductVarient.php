<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVarient extends Model
{
    use HasFactory;
    protected $fillable=['product_id','product_size_id','product_color_id','stock','stock_status'];

    // public function cart_items(){
    //     $this->belongsTo(cartItem::class,'product_varient_id');
    // }

}
