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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::prefix("values")->group(function(){
    Route::get("/",'ValueController@index');
    Route::post("/",'ValueController@store');
    Route::patch("/",'ValueController@store');
    Route::put("/","ValueController@others");
    Route::delete("/","ValueController@others");


});