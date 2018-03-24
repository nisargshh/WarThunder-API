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

Route::get('/statistics/{user}/total', 'TotalStatisticsController@getStatistics');
Route::get('/statistics/{user}/aviation', 'AviationStatisticsController@getStatistics');
Route::get('/statistics/{user}/ground', 'GroundStatisticsController@getStatistics');
Route::get('/statistics/{user}/fleet', 'FleetStatisticsController@getStatistics');
Route::get('/statistics/{user}/vehicle', 'VehicleStatisticsController@getStatistics');
