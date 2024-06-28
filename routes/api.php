<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\SubCategoriesController;
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

// Error Message....
Route::get('/userLogin',function(){
    return response()->json([
        'status' => 404,
        'msg'=>'Please Login First To Access'
    ]);
})->name('userLogin');


// User Register && Login Route && LogOut 

Route::post('/userRegister',[UserController::class,'userRegister']);
Route::post('/userLogin',[UserController::class,'userLogin']);
// Route::get('/userLogout',[UserController::class,'userLogout']);

Route::group(['middleware'=>'auth:api'],function(){
    Route::get('/userLogout',[UserController::class,'userLogout']);
});
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
// Route::middleware('auth:adminApi')->get('/user', function (Request $request) {
//     return $request->user();
// });



Route::post('/authenticate_admin',[AdminAuthController::class,'admin_auth']);

Route::get('/admin_logout',[AdminAuthController::class,'admin_logout'])->middleware('auth:adminApi');


//sunil 28/7
Route::apiResource('/category',CategoriesController::class);
Route::post('/category-search',[CategoriesController::class,'SearchCategory']);

Route::apiResource('/sub-category',SubCategoriesController::class);
Route::post('/sub-category-search',[SubCategoriesController::class,'SearchSubCategory']);
