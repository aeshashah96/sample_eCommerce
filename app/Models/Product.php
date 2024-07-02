<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name','description','price','category_id','sub_category_id','sku','slug','is_featured','long_description'];

    public function category(){
        return $this->belongsTo(Categories::class,'category_id','id');
    }
    public function productImages(){
        return $this->hasMany(ImageProduct::class,'product_id','id');
    }

    public function colors(){
        return $this->belongsToMany(ProductColor::class,'product_varients');
    }

    public function sizes(){
        return $this->belongsToMany(ProductSize::class,'product_varients');
    }
    
    public function productReview(){
        return $this->hasMany(ProductReview::class,'product_id','id');
    }
}
