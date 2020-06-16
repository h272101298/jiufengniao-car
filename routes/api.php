<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('search','home\CarController@search');
Route::get('brandSearch','home\CarController@brandSearch');
Route::post('seniorSearch','home\CarController@car_search');
Route::get('index','home\CarController@index');
Route::get('brand','home\CarController@brand');
Route::get('brand/type','home\CarController@type');
Route::get('entrance','home\FigureController@entrance');
Route::get('advPic','home\FigureController@index');
Route::post('carInfo','home\CarController@car_info');
Route::get('carInfo/detail','home\CarController@car_info_all');
Route::get('config','home\CarController@filtrate');

Route::post('appoint','home\UserController@appoint');
Route::post('custom','home\UserController@custom');
Route::post('collect','home\UserController@collection');
Route::post('collectCancel','home\UserController@collection_cancel');
Route::post('myInfo/CollectionList','home\UserController@CollectionList');
Route::post('myInfo/AppointList','home\UserController@AppointList');

Route::post('login','home\WxController@wxlogin');

Route::post('save','admin\SystemController@formId_Save');


