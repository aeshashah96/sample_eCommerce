<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacts extends Model
{
    use HasFactory;
    // protected $table = 'contacts';
    protected $fillable = ['name','email','subject','message'];
    protected $hidden = ['created_at','updated_at'];
}
