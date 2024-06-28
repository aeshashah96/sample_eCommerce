<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsLatters extends Model
{
    use HasFactory;
    protected $fillable = ['email'];
}
