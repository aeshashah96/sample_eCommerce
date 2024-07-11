<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductColor extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['color'];
    protected $hidden=['created_at','updated_at','pivot'];

    public function products(){
        return $this->belongsToMany(Product::class,'product_varients');
    }
}
