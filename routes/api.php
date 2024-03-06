<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('test',[UserApiController::class,'index']);
Route::post('register',[UserApiController::class,'register']);
Route::post('login',[UserApiController::class,'login']);
// Route::get('getUser/{id}',[UserApiController::class,'getUser']);


Route::middleware('auth:api')->group(function(){
    Route::get('getUser/{id}',[UserApiController::class,'getUser']);
    Route::post('store',[UserApiController::class,'store']);
    Route::get('show/{id}',[UserApiController::class,'show']);
    Route::put('update/{id}',[UserApiController::class,'update']);
    Route::delete('destroy/{id}',[UserApiController::class,'destroy']);
});
