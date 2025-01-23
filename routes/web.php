<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
//    return view('welcome');
    $f = new IntlDateFormatter(
        'ar@calendar=islamic',
        IntlDateFormatter::FULL,
        IntlDateFormatter::FULL,
        'Asia/Gaza',
        IntlDateFormatter::TRADITIONAL,
    );

    echo $f->format(new DateTime());
});
