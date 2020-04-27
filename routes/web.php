<?php

Route::get('/'.config('locations.locations_slug', 'locations'), ['uses' => 'LocationsController@index', 'as' => 'locations']);
Route::get('/'.config('locations.locations_slug', 'locations').'/{slug}', ['uses' => 'LocationsController@get', 'as' => 'locations']);
Route::post('/locations-markers', ['uses' => 'LocationsController@getMarkers']);
