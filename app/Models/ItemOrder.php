<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemOrder extends Model
{
    use HasFactory;
    protected $fillable = ['order_id','product_id','quantity','subtotal'];

    public function product(){
        return $this->belongsTo(Product::class,'product_id','id');
    }
    public function order(){
        return $this->belongsTo(Orders::class,'order_id','id');
    }
}
