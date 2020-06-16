<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::post ('upload','admin\SystemController@upload');
//Route::post('ArrayUpload','admin\SystemController@array_upload');
Route::get('test','admin\CarController@test');

Route::post('login','admin\IndexController@login');
Route::post('logout','admin\IndexController@logout');

//Route::group(['middleware'=>['auth']],function () {
    Route::get('admin','admin\IndexController@index');
    Route::get('admin/add','admin\IndexController@add');
    Route::get('admin/detail','admin\IndexController@editshow');
    Route::post('admin/detail/edit','admin\IndexController@edit');
    Route::post('admin/del','admin\IndexController@del');

    Route::get('role','admin\IndexController@role');
    Route::get('role/add','admin\IndexController@role_add');
    Route::get('role/detail','admin\IndexController@role_edit_show');
    Route::post('role/detail/edit','admin\IndexController@role_edit');
    Route::post('role/del','admin\IndexController@role_del');

    Route::get('figure', 'admin\FigureController@index');
    Route::get('figure/add', 'admin\FigureController@add');
    Route::get('figure/detail', 'admin\FigureController@editshow');
    Route::post('figure/detail/edit', 'admin\FigureController@edit');
    Route::post('figure/del', 'admin\FigureController@del');

    Route::get('brand', 'admin\BrandController@index');
    Route::get('brand/add', 'admin\BrandController@add');
    Route::get('brand/detail', 'admin\BrandController@editshow');
    Route::post('brand/detail/edit', 'admin\BrandController@edit');
    Route::post('brand/del', 'admin\BrandController@del');
    Route::get('brand/type','admin\BrandController@type');
    Route::get('brand/type/add','admin\BrandController@type_add');
    Route::get('brand/type/detail','admin\BrandController@type_edit_show');
    Route::post('brand/type/detail/edit','admin\BrandController@type_edit');
    Route::get('brand/type/check','admin\BrandController@type_check');
    Route::post('brand/type/del','admin\BrandController@type_del');

    Route::get('company','admin\SystemController@company');
    Route::get('company/edit','admin\SystemController@edit_company');
    Route::get('wx','admin\SystemController@wx');
    Route::get('wx/edit','admin\SystemController@edit_wx');

    Route::get('carTotal','admin\CarController@total');

    Route::get('appointTotal','admin\AppointController@total');

    Route::get('brandGet','admin\BrandController@brand');
    Route::get('typeGet','admin\BrandController@gettype');
    Route::get('car/search','admin\CarController@car_search');
//    Route::get('search/brand','admin\BrandController@brand_search');
//    Route::get('search/age','admin\BrandController@age_search');
//    Route::get('search/text','admin\BrandController@text_search');

    Route::get('car','admin\CarController@index');

    Route::get('car/click','admin\CarController@click');
    Route::post('car/add','admin\CarController@add');
    Route::get('car/show','admin\CarController@edit_show');
    Route::post('car/show/edit','admin\CarController@edit');
    Route::post('car/del','admin\CarController@del');
//    Route::get('car/info','admin\CarController@info');

    Route::get('car/basic','admin\CarController@basic');

    Route::get('car/basic/add','admin\CarController@basic_add');
    Route::get('car/basic/show','admin\CarController@basic_edit_show');
    Route::post('car/basic/show/edit','admin\CarController@basic_edit');
    Route::post('car/basic/del','admin\CarController@basic_del');

    Route::get('car/config','admin\CarController@parent');
    Route::get('car/config/add','admin\CarController@parent_add');
    Route::post('car/config/del','admin\CarController@parent_del');
    Route::get('car/config/detail','admin\CarController@detail');
    Route::get('car/config/detail/add','admin\CarController@detail_add');
    Route::post('car/config/detail/edit','admin\CarController@detail_edit');
    Route::post('car/config/detail/del','admin\CarController@detail_del');
    Route::get('car/config/detail/val','admin\CarController@detail_val');
    Route::get('car/config/detail/val/add','admin\CarController@detail_val_add');
    Route::post('car/config/detail/val/edit','admin\CarController@detail_val_edit');

    Route::get('appoint','admin\AppointController@info');
    Route::get('appoint/detail','admin\AppointController@detail');
    Route::get('appoint/click','admin\AppointController@click');
    Route::get('appoint/search','admin\AppointController@search');
    Route::post('appoint/del','admin\AppointController@del');

    Route::get('config','admin\CarController@search_menu');
    Route::get('config/detail','admin\CarController@search');
    Route::get('config/detail/add','admin\CarController@search_add');
    Route::post('config/detail/edit','admin\CarController@search_edit');
    Route::post('config/detail/del','admin\CarController@search_del');

    Route::get('info','admin\InfoController@index');
    Route::post('caltest','admin\InfoController@test');

    Route::post('MesSend','admin\SystemController@sendmes');

    Route::get('custom','admin\AppointController@custom');
    Route::post('custom/click','admin\AppointController@custom_click');
    Route::post('custom/del','admin\AppointController@custom_delete');
//});
