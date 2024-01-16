<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::apiResource('/users', 'UsersController')->names('api.users')->parameters([
        'users' => 'user'
    ]);

    // Route::apiResource('/mensagens', 'MensagensController')->names('api.mensagens');
    Route::post('/mensagens/store', 'MensagensController@store')->name('api.mensagens.store');
    Route::get('/mensagens-list/{user}', 'MensagensController@listMensages')->name('api.mensagens-list');
});
