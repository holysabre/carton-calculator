<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('route_class')) {
    function route_class()
    {
        return str_replace('.', '-', Route::currentRouteName());
    }
}
