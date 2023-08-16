<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "ObservationController@get");
Route::get("get/provinces", "ObservationController@getProvince");
Route::get("get/approved", "ObservationController@getApproved");
Route::get("get/detail/{id}", "ObservationController@getDetail");
Route::get("approve/{id}", "ObservationController@approve");
Route::get("reject/{id}", "ObservationController@reject");
Route::post("edit", "ObservationController@edit");
Route::delete("delete/{id}", "ObservationController@delete");
Route::delete("comment/{id}", "ObservationController@deleteComment");
