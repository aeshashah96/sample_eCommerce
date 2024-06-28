<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\BannerCrudController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Mismatch Message....
Route::get('/error',function(){
    return response()->json([
        'status' => 404,
        'msg'=>'Mismatch Token ..'
    ]);
})->name('login');


// User Register && Login Route && LogOut 
Route::post('/userRegister',[UserController::class,'userRegister']);
Route::post('/userLogin',[UserController::class,'userLogin']);

Route::group(['middleware'=>'auth:api'],function(){
    Route::get('/userLogout',[UserController::class,'userLogout']);
});


Route::post('/authenticateadmin',[AdminAuthController::class,'admin_auth']);

// harshvardhan 28 jun handle logout route errors
Route::get('/error',function(){
    return response()->json([
        'status' => 404,
        'msg'=>'Mismatch Token ..'
    ]);
})->name('login');


// harshvardhan 28 jun logout route
Route::middleware(['auth:adminApi'])->group(function(){
    Route::get('/adminlogout',[AdminAuthController::class,'admin_logout']);
    Route::get('/adminprofile',[AdminAuthController::class,'fetch_admin_data']);
    Route::post('/editadminprofile',[AdminAuthController::class,'edit_admin_data']);
    Route::post('/changeadminpassword',[AdminAuthController::class,'change_admin_password']);
    Route::resource('/banner',BannerCrudController::class);
});
// AdminLogin  && AdminLogout
Route::post('/authenticate_admin',[AdminAuthController::class,'admin_auth']);
Route::get('/admin_logout',[AdminAuthController::class,'admin_logout'])->middleware('auth:adminApi');


