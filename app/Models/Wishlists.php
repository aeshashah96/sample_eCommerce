<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlists extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','product_id'];
    protected $hidden = ['created_at','updated_at',];

    public function users(){
        return $this->belongsTo(User::class);
    }
    public function products(){
        return $this->belongsTo(Product::class,'product_id','id');
    }
    public function reviews(){
        return $this->hasMany(ProductReview::class,'product_id','product_id');
    }
}
