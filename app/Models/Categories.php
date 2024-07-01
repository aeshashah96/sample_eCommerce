<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;
    protected $fillable = ['name','description','category_image','is_Active'];

    public function subCategories(){
       return $this->hasMany(SubCategories::class,'category_id','id');
    }
    
    public function products(){
        return $this->hasMany(Product::class,'category_id','id');
    }
    public function subCategory(){
        return $this->hasMany(SubCategories::class,'category_id','id');
    }
}
