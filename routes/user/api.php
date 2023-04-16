<?php

use App\Constants\ApiConstant;
use App\Constants\TokenConstant;
use Illuminate\Support\Facades\Route;

Route::prefix(ApiConstant::PREFIX_AUTH)->namespace(ucfirst(ApiConstant::PREFIX_AUTH))->group(__DIR__ . "/" . ApiConstant::PREFIX_AUTH . ".php");
Route::namespace(ucfirst(ApiConstant::PREFIX_USER))->group(function () {
    Route::prefix(ApiConstant::PREFIX_OBSERVATION)->group(__DIR__ . "/" . ApiConstant::PREFIX_OBSERVATION . ".php");

    Route::middleware([TokenConstant::AUTH_SANCTUM, TokenConstant::AUTH_USER])->group(function () {
        Route::prefix(ApiConstant::PREFIX_NOTIFICATION)->group(__DIR__ . "/" . ApiConstant::PREFIX_NOTIFICATION . ".php");
    });
});
