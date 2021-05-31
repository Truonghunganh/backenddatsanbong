<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

$app->get('/clear-cache', function () {
    $code = Artisan::call('cache:clear');
    return 'cache cleared';
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('/clear-cache', function () {
//     $exitCode = Artisan::call('cache:clear');
//     // return what you want
// });


// Route::get('/clear-cache', function () {
//     Artisan::call('cache:clear');
//     return "Cache is cleared";
// });

Route::get('/welcome', function () {
    return view('welcome');
});
Route::get('/', function () {
    return view('welcome');
});
