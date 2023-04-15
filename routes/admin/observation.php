<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "ObservationController@get");
Route::get("approve/{id}", "ObservationController@approve");
Route::get("reject/{id}", "ObservationController@reject");
