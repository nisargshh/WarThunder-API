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

use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;

Route::get('/swagger/json', function(){
  return response(\File::get(public_path() . '/json/swagger.json'))->withHeaders(['Content-Type' => 'application/json']);
});

Route::get('/' , 'HomeController@index');
Route::get('/test', 'ReplaysController@index');
