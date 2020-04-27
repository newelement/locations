<?php

Route::get('/locations/settings', ['uses' => 'LocationsController@getSettings', 'as' => 'locations']);
Route::post('/locations/settings', ['uses' => 'LocationsController@updateSettings', 'as' => 'locations']);
Route::get('/locations/levels', ['uses' => 'LocationsController@indexLevels', 'as' => 'locations']);
Route::get('/locations/levels/{level}', ['uses' => 'LocationsController@getLevel', 'as' => 'locations']);
Route::put('/locations/levels/{level}', ['uses' => 'LocationsController@updateLevel', 'as' => 'locations']);
Route::delete('/locations/levels/{level}', ['uses' => 'LocationsController@deleteLevel', 'as' => 'locations']);
Route::post('/locations/levels', ['uses' => 'LocationsController@createLevel', 'as' => 'locations']);

Route::get('/locations', ['uses' => 'LocationsController@index', 'as' => 'locations']);
Route::get('/locations/create', ['uses' => 'LocationsController@show', 'as' => 'locations']);
Route::get('/locations/{location}', ['uses' => 'LocationsController@get', 'as' => 'locations']);
Route::post('/locations', ['uses' => 'LocationsController@create', 'as' => 'locations']);
Route::put('/locations/{location}', ['uses' => 'LocationsController@update', 'as' => 'locations']);
Route::delete('/locations/{location}', ['uses' => 'LocationsController@delete', 'as' => 'locations']);

