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

Route::get('/socialite/redirect/{provider}', 'Auth\AuthController@socialiteRedirect');
Route::get('/socialite/callback/{provider}', 'Auth\AuthController@socialiteCallback');

Route::get('/', function () {
    dd(\Illuminate\Support\Facades\DB::table('countries')->get());
    return view('welcome');
});


Route::group([
    'prefix' => 'test'
], function ($router) {
    Route::get('/', 'TestController@index');
});