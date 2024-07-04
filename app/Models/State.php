<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $fillable = [
        'state_name',
        'country_id',
        'status'
    ];
    public function cityData()
    {
        return $this->hasMany(City::class,'state_id','id');
    }
    public function get_country_from_state()
    {
        return $this->belongsTo(Country::class,'country_id','id');
    }
}
