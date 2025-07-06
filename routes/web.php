<?php

use App\Http\Controllers\ClassroomsController;
use App\Http\Controllers\JoinClassroomController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TopicsController;
use Illuminate\Support\Facades\Route;


//Route::resource('/classrooms', ClassroomsController::class);
// here possible change the name of parameter.
//    ->parameter('classrooms', 'classroom');
//    ->where(['classrooms' => '\d+']);


Route::resources([
    'classrooms' => ClassroomsController::class,
    'topics' => TopicsController::class,
]);

Route::get('classrooms/{classroom}/join', [JoinClassroomController::class, 'create'])->name('classrooms.create');
Route::post('classrooms/{classroom}/join', [JoinClassroomController::class, 'store'])->name('classrooms.join');

Route::get('login', [LoginController::class, 'create'])->name('login');
Route::post('login', [LoginController::class, 'store'])->name('login.store');

//Route::get('/', function () {
////    return view('welcome');
//    $f = new IntlDateFormatter(
//        'ar@calendar=islamic',
//        IntlDateFormatter::FULL,
//        IntlDateFormatter::FULL,
//        'Asia/Gaza',
//        IntlDateFormatter::TRADITIONAL,
//    );
//
//    echo $f->format(new DateTime());
//});
