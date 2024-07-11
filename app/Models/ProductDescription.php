<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDescription extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable=['product_id','description','additional_information'];

    protected $hidden=['created_at','updated_at'];
}
