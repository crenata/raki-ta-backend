<?php

use App\Constants\TokenConstant;
use Illuminate\Support\Facades\Route;

Route::get("get", "ObservationController@get");
Route::get("get/provinces", "ObservationController@getProvince");
Route::get("get/detail/{id}", "ObservationController@getDetail");
Route::middleware([TokenConstant::AUTH_SANCTUM, TokenConstant::AUTH_USER])->post("add", "ObservationController@add");
