<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['guest:api']], function () {
    Route::post('login', 'Api\AuthController@login');
    Route::post('signup', 'Api\AuthController@signup');

    //home screen api's
    Route::get('homescreen', 'Api\HomeScreenController@getHomeScreenData');
    Route::get('all-categories', 'Api\HomeScreenController@getAllCategories');
    Route::get('all-popular-products', 'Api\HomeScreenController@getAllPopularProducts');
    Route::get('category-products', 'Api\HomeScreenController@getCategoryProducts');
    Route::get('product-details', 'Api\HomeScreenController@productDetails');
    Route::post('search', 'Api\SearchController@keywordSearch');
    Route::get('get-business-loc', 'Api\HomeScreenController@getBusinessLoc');
    Route::get('get-products-list', 'Api\HomeScreenController@allProductList');


    //cart apis
    Route::get('get-cart-products', 'Api\CartController@getCartProducts');
    Route::post('save-order', 'Api\CartController@saveOrder');

    //admin apis
    Route::post('admin-login', 'Api\AdminAuthController@login');

    Route::get('get-completed-orders', 'Api\OrderController@getCompletedOrders');
    Route::get('get-pending-orders', 'Api\OrderController@getPendingOrders');
    Route::get('edit-order', 'Api\OrderController@editOrder');
    Route::post('update-order', 'Api\OrderController@updateOrder');
    Route::post('change-status', 'Api\OrderController@changeStatus');

});

Route::get('logout', 'Api\AuthController@logout');
Route::get('user', 'Api\AuthController@getUser');
Route::post('update-profile', 'Api\AuthController@updateProfile');

