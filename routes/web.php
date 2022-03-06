<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});
Route::group(['middleware' => 'auth'], function (){
    Route::get('dashboard', [ShopController::class, 'storeManage'])->name('dashboard');
    Route::post('store-add', [ShopController::class, 'storeAdd'])->name('store-add');
    Route::get('store-product/{id}', [ProductController::class, 'storeProduct'])->name('store-product');
    Route::get('yahoo-auth-code', [ProductController::class, 'yahooAuthCode'])->name('yahoo-auth-code');
    Route::get('yahoo_callback', [ProductController::class, 'yahooCallback'])->name('yahoo_callback');
    Route::get('yahoo-get-category/{id}', [ProductController::class, 'yahooGetCategory'])->name('yahoo-get-category');
    Route::get('yahoo-search-product/{id}', [ProductController::class, 'yahooSearchProduct'])->name('yahoo-search-product');
});
//Route::get('/dashboard', function () {
//    return view('dashboard');
//})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
