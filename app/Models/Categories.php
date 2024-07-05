<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Categories extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'category_image', 'category_slug','is_Active'];
    protected $hidden=['description','created_at','updated_at'];

    public function setTitleAttribute($value)
    {
        $this->attributes['name'] = $value;
        if (!$this->slug) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
    public function subcategory()
    {
        return $this->hasMany(SubCategories::class, 'category_id', 'id');
    }
}
