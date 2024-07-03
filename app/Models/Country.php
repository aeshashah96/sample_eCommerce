<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_name',
        'status'
    ];
    public function get_states()
    {
        return $this->hasMany(State::class,'country_id','id');
    }
}
