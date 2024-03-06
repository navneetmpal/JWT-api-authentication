<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\front\IndexController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('index', [IndexController::class,'index']);
Route::get('getdata/{id}', [IndexController::class,'getdata'])->name('get.data');
Route::post('update', [IndexController::class,'update'])->name('update');
// --------------------------------------------------
// --------------------------------------------------
Route::post('file-upload', [IndexController::class, 'FileUpload' ])->name('FileUpload');
Route::get('imagedelete', [IndexController::class, 'imagedelete' ])->name('image.delete');
// Route::get('delete-image/{imageId}', 'IndexController@imagedelete')->name('image.delete');

