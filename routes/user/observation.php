<?php

use App\Constants\TokenConstant;
use Illuminate\Support\Facades\Route;

Route::get("get", "ObservationController@get");
Route::middleware([TokenConstant::AUTH_SANCTUM, TokenConstant::AUTH_USER])->post("add", "ObservationController@add");
