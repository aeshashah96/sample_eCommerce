<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banners extends Model
{
    use HasFactory;
    protected $fillable = [
        'image',
        'description',
        'banner_title',
        'banner_url',
        'sub_category_id'
    ]; 
    public function getSubCategory()
    {
        return $this->belongsTo(SubCategories::class,'sub_category_id','id');
    }
}
