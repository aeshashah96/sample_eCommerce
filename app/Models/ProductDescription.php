<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDescription extends Model
{
    use HasFactory;

    protected $fillable=['product_id','description','additional_information'];

    protected $hidden=['created_at','updated_at','description'];
}
