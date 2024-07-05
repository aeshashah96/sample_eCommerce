<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','order_no','order_status','payment_method','total_price'];

    public function item_order(){
        return $this->hasMany(ItemOrder::class,'order_id','id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
