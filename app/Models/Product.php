<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name','description','price','category_id','sub_category_id','sku','slug','is_featured','long_description','isActive'];

    public $hidden=['created_at','updated_at'];
    public function category(){
        return $this->belongsTo(Categories::class,'category_id','id');
    }
    public function subcategory(){
        return $this->belongsTo(SubCategories::class,'sub_category_id','id');
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
    // Wishlist Relationship
    public function wishlists(){
        return $this->hasMany(Wishlists::class);
    }
    public function subcategory(){
        return $this->belongsTo(SubCategories::class,'sub_category_id','id');
    }
    public function productInformation(){
        return $this->hasOne(ProductDescription::class,'product_id','id');
    }

    public function item_orders(){
        return $this->hasMany(ItemOrder::class,'product_id','id');
    }

}
