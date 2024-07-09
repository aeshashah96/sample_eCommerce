<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\SubCategoriesController;
use App\Http\Controllers\BannersController;
use App\Http\Controllers\CartsController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FeaturesController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\NewsLetterController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductColorController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistsController;
use App\Http\Controllers\ProductSizeController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\UserAddressesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
Route::get('/error', function () {
    return response()->json(
        [
            'success' => false,
            'status' => 401,
            'message' => 'Mismatch Token ..',
        ],
        401,
    );
})->name('login');

// <---------------------------- User Module : Nehal Solanki------------------------------------------->

// User Register && Login Route && LogOut
Route::post('/user-register', [UserController::class, 'userRegister']);
Route::post('/user-login', [UserController::class, 'userLogin']);

Route::get('/email/verify', [UserController::class,'verify'])->name('verification.notice');
// User Logout && My Profile && Forgot-Password && Reset Password
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/featured-product', [ProductController::class, 'list_featured_product'])->middleware('verified');
    Route::get('/user-logout', [UserController::class, 'userLogout']);
    Route::get('/my-profile', [UserController::class, 'userProfile']);
    Route::post('/update-profile', [UserController::class, 'updateProfile']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
});

Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('/otp-verification', [UserController::class, 'otpVerification']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);
// <------------------------------ User Module Completed ------------------------------------------------->

// <------------------------------ login module ------------------------------------------------->
Route::post('/admin-login', [AdminAuthController::class, 'admin_login']);
// <------------------------------ login module Completed ------------------------------------------------->

// harshvardhan 28 jun logout route
Route::middleware(['auth:adminApi'])->group(function () {
    // <------------------------------ Admin profile module ------------------------------------------------->
    Route::get('/admin-logout', [AdminAuthController::class, 'admin_logout']);
    Route::get('/admin-profile', [AdminAuthController::class, 'admin_profile']);
    Route::post('/edit-admin-profile', [AdminAuthController::class, 'edit_admin_profile']);
    Route::post('/change-admin-password', [AdminAuthController::class, 'change_admin_password']);
    // <------------------------------end of Admin profile module ------------------------------------------------->

    // <------------------------------ Banner module ------------------------------------------------->
    Route::resource('/banner', BannersController::class);
    // <------------------------------end of Banner module ------------------------------------------------->

    // <------------------------------ newsletter module ------------------------------------------------->
    Route::apiResource('newsletter', NewsLetterController::class);
    // <------------------------------end of newsletter module ------------------------------------------------->

    // <------------------------------ setting module ------------------------------------------------->
    Route::get('get-setting', [SettingController::class, 'getSettingData']);
    Route::post('update-setting', [SettingController::class, 'updateSettingData']);
    // <------------------------------end of setting module ------------------------------------------------->

    // <------------------------------ customer module ------------------------------------------------->
    Route::apiResource('customer', CustomerController::class);
    // <------------------------------end of customer module ------------------------------------------------->

    // <------------------------------ language module ------------------------------------------------->
    Route::apiResource('language', LanguageController::class);
    // <------------------------------end of language module ------------------------------------------------->

    // <------------------------------ city module ------------------------------------------------->
    Route::get('get-cities', [CityController::class, 'get_cities']);
    Route::delete('delete-city/{id}', [CityController::class, 'delete_city']);
    Route::post('edit-city/{id}', [CityController::class, 'edit_city']);
    Route::get('view-city/{id}', [CityController::class, 'view_city']);
    // <------------------------------end of city module ------------------------------------------------->

    // <------------------------------state module ------------------------------------------------->
    Route::apiResource('state', StateController::class);
    // <------------------------------end of state module ------------------------------------------------->

    // <------------------------------country module ------------------------------------------------->
    Route::apiResource('country', CountryController::class);
    // <------------------------------end of country module ------------------------------------------------->

    //sunil 28/6

    // <-------------------------- Category Crud : Sunil Sorani : 28/6/2024 ------------------------------>

    Route::apiResource('/category', CategoriesController::class);
    Route::post('/category-search', [CategoriesController::class, 'SearchCategory']);
    Route::get('/category-status/{id}', [CategoriesController::class, 'changeActiveStatus']);

    // <-------------------------- SubCategory Crud : Sunil Sorani : 28/6/2024 ------------------------------>
    Route::apiResource('/sub-category', SubCategoriesController::class);
    Route::post('/sub-category-search', [SubCategoriesController::class, 'SearchSubCategory']);

    Route::get('/subcategory-status/{id}', [SubCategoriesController::class, 'changeActiveStatus']);

    //sunil product module
    Route::apiResource('product', ProductController::class);
    Route::apiResource('product-color', ProductColorController::class);
    Route::apiResource('product-size', ProductSizeController::class);
    Route::get('/get-product/{id}', [ProductController::class, 'getProduct']);
    Route::get('/remove-product-image/{id}', [ProductController::class, 'removeImageOfProduct']);

Route::get('/subcategory-status/{id}',[SubCategoriesController::class,'changeActiveStatus']);

//sunil product module
Route::apiResource('product',ProductController::class);
Route::apiResource('product-color',ProductColorController::class);
Route::apiResource('product-size',ProductSizeController::class);
Route::get('/get-product/{id}',[ProductController::class,'getProduct']);
Route::get('/remove-product-image/{id}',[ProductController::class,'removeImageOfProduct']);

Route::apiResource('order',OrdersController::class);
Route::get('/contact-us',[ContactsController::class,'getAllContactUs']);
Route::get('/contact-us/{id}',[ContactsController::class,'showDetailsOfCountectUs']);


Route::get('/product-status/{id}',[ProductController::class,'changeActiveStatus']);
Route::get('/banner-status/{id}',[BannersController::class,'changeStatus']);
Route::get('/language-status/{id}',[LanguageController::class,'changeStatus']);

Route::get('/city-status/{id}',[CityController::class,'changeActiveStatus']);
Route::get('/state-status/{id}',[StateController::class,'changeActiveStatus']);
Route::get('/country-status/{id}',[CountryController::class,'changeActiveStatus']);





});
// AdminLogin  && AdminLogout


//get setting api.............................................................
Route::get('get-setting',[SettingController::class,'getSettingData']);


    Route::apiResource('order', OrdersController::class);

// AdminLogin  && AdminLogout

// 28/06 Category Show Get Api  Nikunj
Route::get('/list-category', [CategoriesController::class, 'listCategory']);
Route::post('/add-category', [CategoriesController::class, 'addCategory']);

// 1st July Banner Get Api For front end side Nikunj
Route::get('/home-banner', [BannersController::class, 'homeBanner']);

// 28/06 Sub Category Show Get Api Nikunj
Route::get('/list-subcategory', [SubCategoriesController::class, 'listSubCategory']);
Route::post('add-subcategory', [SubCategoriesController::class, 'addSubCategory']);

// 01/07 Add ContactUs Post Api Nikunj

Route::post('/add-contact', [ContactsController::class, 'addContactUs']);

// 01/07 Add NewsLetter Post Api Front Side Nikunj

Route::post('/news-letter', [NewsLetterController::class, 'addNewsLetter']);
Route::get('/show-subcategory', [SubCategoriesController::class, 'showSubCategory']);

// 02/07 List Product of featured Nikunj

// <------------------------- Wishlist Module : Nehal Solanki : 2/7/2024 ------------------------->
// Add Product , Show , Delete In Wishlist
Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/add-product/wishlist/{id}', [WishlistsController::class, 'addRemoveProductWishlist']);
    Route::get('/show-product/wishlist', [WishlistsController::class, 'showProductWishlists']);
});
// <------------------------ Wishlist Module Completed : Nehal Solanki -------------------------->

Route::get('/get-product/{id}', [ProductController::class, 'getProduct']);

// <-------------------------- Cart Module : Nehal Solanki : 3/7/2024 ------------------------------>
Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/add-product/cart/{id}', [CartsController::class, 'addProductCart']);
    Route::get('/show-product/cart', [CartsController::class, 'showCartProduct']);
    Route::post('/update-item/add/cart/{id}', [CartsController::class, 'addItem']);
    Route::post('/update-item/remove/cart/{id}', [CartsController::class, 'removeItem']);
    Route::delete('/delete-product/cart/{id}', [CartsController::class, 'deleteCartProduct']);

    // <------------------------------------------------------------------------------------------------>

    // <-------------------------- User Address Module : Harshvardhan Zala : 4/7/2024 ------------------------------>
    Route::get('get-user-address/{id}', [UserAddressesController::class, 'get_user_address']);
    Route::post('add-user-address', [UserAddressesController::class, 'add_user_address']);
    Route::post('edit-user-address/{id}', [UserAddressesController::class, 'edit_user_address']);
    Route::get('get-city', [CityController::class, 'get_cities']);
    Route::get('search-city/{id}', [CityController::class, 'search_city']);
    Route::get('select-city/{id}', [CityController::class, 'select_city']);
    // <------------------------------------------------------------------------------------------------>

    // <-------------------------- search Module : Harshvardhan Zala : 4/7/2024 ------------------------------>
    Route::get('search-filter/{id}', [FeaturesController::class, 'search_filter']);
    // <------------------------------------------------------------------------------------------------>

    Route::post('/add-product-review/{id}', [ProductController::class, 'addProductReview']);
});
// <------------------------------------------------------------------------------------------------>

// ------------------------------Product Details APi : Nikunj -------------------------------



// <-------------------------- search Module : Harshvardhan Zala : 4/7/2024 ------------------------------> 
Route::post('/fitler',[FeaturesController::class,'filter_product']);

Route::get('search/{id}',[FeaturesController::class,'search_by_vatiant']);
Route::post('/varient-fitler',[FeaturesController::class,'filter_by_vatiant']);


// <------------------------------------------------------------------------------------------------>

Route::get('/get-product/{slug}', [ProductController::class, 'getProduct']);
Route::get('/get-product-details', [ProductController::class, 'productAdditionalInformation']);
Route::get('/get-product-review', [ProductController::class, 'productReview']);

Route::group(['middleware' => 'guest:api'], function () {
    Route::get('/list-featured-product', [ProductController::class, 'list_featured_product']);
    Route::get('/get-related-product/{slug}', [ProductController::class, 'getRelatedProduct']);
});
