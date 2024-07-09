<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItemOrdersController extends Controller
{
    protected $attributes = ['productName','productPrice'];
    protected $fillable = ['order_id','product_id','quantity','subtotal','product_varients_id'];
}
