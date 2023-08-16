<?php

use App\Constants\TokenConstant;
use Illuminate\Support\Facades\Route;

Route::post("register", "UserController@register");
Route::post("login", "UserController@login");
Route::middleware([TokenConstant::AUTH_SANCTUM, TokenConstant::AUTH_USER])->group(function () {
    Route::get("self", "UserController@self");
    Route::get("logout", "UserController@logout");
});
