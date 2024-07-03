<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubCategories extends Model
{
    use HasFactory;
    protected $fillable = ['category_id','name','subcategory_slug'];

    public function setTitleAttribute($value)
    {
        $this->attributes['name'] = $value;
        if (!$this->slug) {
            $this->attributes['subcategory_slug'] = Str::slug($value);
        }
    }

    protected $hidden=['created_at','updated_at'];
    public function products(){
        return $this->hasMany(Product::class,'sub_category_id','id');
    }

    public function category(){
        return $this->belongsTo(Categories::class,'category_id','id');
    }

}
