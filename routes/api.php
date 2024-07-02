<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\SubCategoriesController;
use App\Http\Controllers\BannersController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\NewsLetterController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProductController;
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
        'success'=>false,
        'status' => 404,
        'message'=>'Mismatch Token ..'
    ]);
})->name('login');

// <---------------------------- User Module : Nehal Solanki------------------------------------------->

// User Register && Login Route && LogOut 
Route::post('/userRegister',[UserController::class,'userRegister']);
Route::post('/userLogin',[UserController::class,'userLogin']);

// User Logout && My Profile && Forgot-Password && Reset Password 
Route::group(['middleware'=>'auth:api'],function(){
    Route::get('/userLogout',[UserController::class,'userLogout']);
    Route::get('/my-profile',[UserController::class,'userProfile']);
    Route::post('/update-profile',[UserController::class,'updateProfile']);
    Route::post('/change-password',[UserController::class,'changePassword']);
});

Route::post('/admin-login',[AdminAuthController::class,'admin_login']);
Route::post('/forgot-password',[UserController::class,'forgotPassword']);
Route::get('/reset-password',[UserController::class,'resetPassword'])->name('password.reset');
// <------------------------------ User Module Completed ------------------------------------------------->

Route::post('/authenticateadmin',[AdminAuthController::class,'admin_auth']);

// harshvardhan 28 jun handle logout route errors
// Route::get('/error',function(){
//     return response()->json([
//         'status' => 404,
//         'msg'=>'Mismatch Token ..'
//     ]);
// })->name('login');


// harshvardhan 28 jun logout route
Route::middleware(['auth:adminApi'])->group(function(){
    Route::get('/admin-logout',[AdminAuthController::class,'admin_logout']);
    Route::get('/admin-profile',[AdminAuthController::class,'admin_profile']);
    Route::post('/edit-admin-profile',[AdminAuthController::class,'edit_admin_profile']);
    Route::post('/change-admin-password',[AdminAuthController::class,'change_admin_password']);
    Route::resource('/banner',BannersController::class);

    // harshvardhan 1 jul news letter task 
    Route::apiResource('newsletter',NewsLetterController::class);

    Route::get('get-setting',[SettingController::class,'getSettingData']);
    Route::post('update-setting',[SettingController::class,'updateSettingData']);
});
// AdminLogin  && AdminLogout


//sunil 28/6
Route::apiResource('/category',CategoriesController::class);
Route::post('/category-search',[CategoriesController::class,'SearchCategory']);

Route::apiResource('/sub-category',SubCategoriesController::class);
Route::post('/sub-category-search',[SubCategoriesController::class,'SearchSubCategory']);


// 28/06 Category Show Get Api  Nikunj
Route::get('/list-category',[CategoriesController::class,'listCategory']);
// 1st July Banner Get Api For front end side Nikunj 
Route::post('/createbanner',[BannersController::class,'bannerCreate']);

Route::post('/createbanner',[BannersController::class,'bannerCreate']);

// 1st July Banner Get Api For front end side Nikunj 
Route::get('/home-banner',[BannersController::class,'homeBanner']);

// 28/06 Sub Category Show Get Api Nikunj
Route::get('/list-subcategory',[SubCategoriesController::class,'listSubCategory']);

// 01/07 Add ContactUs Post Api Nikunj 

Route::post('/add-contact',[ContactsController::class,'addContactUs']);

// 01/07 Add NewsLetter Post Api Front Side Nikunj 

Route::post('/add-news-letter',[NewsLetterController::class,'addNewsLetter']);
Route::get('/show-subcategory',[SubCategoriesController::class,'showSubCategory']);

//sunil 1/7

Route::apiResource('product',ProductController::class);

Route::get('/list-featured-product',[ProductController::class,'list_featured_product']);
