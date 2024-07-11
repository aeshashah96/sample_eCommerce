<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVarient extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable=['product_id','product_size_id','product_color_id','stock','stock_status','variant_name'];
    protected $hidden = ['product_id','product_size_id','product_color_id','stock'];

    public function products(){
        return $this->belongsTo(Product::class,'product_id','id');
    }

}
