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
    return view('auth.login');
});
Route::group(['middleware' => 'auth'], function (){
    Route::get('dashboard', [ShopController::class, 'appManage'])->name('dashboard');
    Route::post('app-add', [ShopController::class, 'appAdd'])->name('app-add');
    Route::post('app-delete', [ShopController::class, 'appDelete'])->name('app-delete');
    Route::get('store-manage', [ShopController::class, 'storeManage'])->name('store-manage');
    Route::post('store-add', [ShopController::class, 'storeAdd'])->name('store-add');
    Route::post('store-delete', [ShopController::class, 'storeDelete'])->name('store-delete');
    Route::get('store-product/{id}', [ProductController::class, 'storeProduct'])->name('store-product');
    Route::post('product-list', [ProductController::class, 'productList'])->name('product-list');
    Route::get('yahoo-auth-code/{id}', [ProductController::class, 'yahooAuthCode'])->name('yahoo-auth-code');
    Route::get('yahoo_callback', [ProductController::class, 'yahooCallback'])->name('yahoo_callback');
    Route::get('yahoo-refresh', [ProductController::class, 'yahooRefresh'])->name('yahoo-refresh');
    Route::get('yahoo-get-category/{id}', [ProductController::class, 'yahooGetCategory'])->name('yahoo-get-category');
    Route::get('yahoo-search-product/{id}', [ProductController::class, 'yahooSearchProduct'])->name('yahoo-search-product');
    Route::get('yahoo-product-item/{id}', [ProductController::class, 'yahooProductItem'])->name('yahoo-product-item');
    Route::get('yahoo-get-product-detail', [ProductController::class, 'yahooGetProductDetail'])->name('yahoo-get-product-detail');
});
//Route::get('/dashboard', function () {
//    return view('dashboard');
//})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
