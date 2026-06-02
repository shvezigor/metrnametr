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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::namespace('Client')->middleware(['auth'])->group(function() {
    Route::get('client', ['uses' => 'AccountController@index', 'as' => 'client.index']);
});

Route::namespace('Api')->middleware('auth')->group(function() {

    // BEGIN Settings
    Route::get('/settings', 'SettingsController@index');
    Route::get('/setting/{key}', 'SettingsController@show');
    Route::post('/settings/slider', 'SettingsController@slider');
    Route::post('/setting/{key}', 'SettingsController@update');
    Route::post('/settings', 'SettingsController@update');
    // END Settings

    // Upload
    Route::post('products/{id}/upload', 'ProductsController@upload');
    Route::post('articles/{id}/upload', 'ArticlesController@upload');

    Route::get('users/list', "UsersController@list");
    Route::get('users/list/array', "UsersController@listOfArray");

    Route::get('categories/list', "CategoriesController@list");
    Route::get('categories/list/array', "CategoriesController@listOfArray");

    Route::get('catalog/list', "CatalogController@list");
    Route::get('catalog/list/array', "CatalogController@listOfArray");

    Route::get('sizes/list', "SizesController@list");
    Route::get('sizes/list/array', "SizesController@listOfArray");

    Route::get('types/list', "TypesController@list");
    Route::get('types/list/array', "TypesController@listOfArray");

    // Custom
    Route::get('products/labels', 'ProductsController@labels');
    Route::delete('images/{id}', "ImagesController@destroy");

    Route::resource('users', 'UsersController')->except(['create', 'edit']);
    Route::resource('orders', 'OrdersController')->except(['create', 'edit', 'update', 'show']);
    Route::resource('subscribers', 'SubscribersController')->except(['create', 'edit', 'update', 'show']);
    Route::resource('messages', 'MessagesController')->except(['create', 'edit', 'update', 'show']);
    Route::resource('articles', 'ArticlesController')->except(['create', 'edit']);
    Route::resource('vacancies', 'VacanciesController')->except(['create', 'edit']);
    Route::resource('catalog', 'CatalogController')->except(['create', 'edit']);
    Route::resource('categories', 'CategoriesController')->except(['create', 'edit']);
    Route::resource('products', 'ProductsController')->except(['create', 'edit']);
    Route::resource('sizes', 'SizesController')->except(['create', 'edit']);
    Route::resource('types', 'TypesController')->except(['create', 'edit']);
});
