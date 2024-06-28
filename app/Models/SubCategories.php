<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategories extends Model
{
    use HasFactory;
    protected $fillable = ['category_id','name'];
    public function Categories(){
       return $this->belongsTo(Categories::class,'category_id','id');
    }
}
