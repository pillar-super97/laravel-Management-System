<?php
use Illuminate\Http\Request;
//Route::group(['prefix' => '/v1', 'namespace' => 'Api\V1', 'as' => 'api.'], function () {
    //Route::get('getUserData', 'ApiController@getUserData');
    Route::post('/login', 'ApiController@authenticate');
    Route::group(['middleware' => ['jwt.auth']], function() {
        Route::get('getEventData', 'ApiController@getEventData');
        Route::get('getUserData', 'ApiController@getUserData');
        //Route::get('getEmployeeData', 'ApiController@getEmployeeData');
    });
//});
