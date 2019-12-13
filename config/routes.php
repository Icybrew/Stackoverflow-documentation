<?php

use App\Core\Route;

Route::get("/", "indexController@index");
Route::get("/error", "errorController@index");
