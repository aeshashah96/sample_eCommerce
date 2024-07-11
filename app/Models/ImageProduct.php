<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImageProduct extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'product_images';
    protected $fillable=['product_id','image'];
    protected $hidden=['created_at','updated_at'];
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
