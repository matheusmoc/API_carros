<?php

use App\Http\Controllers\{
    ClienteController,
    CarroController,
    LocacaoController,
    MarcaController,
    ModeloController,
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('cliente', ClienteController::class);
Route::apiResource('carro', CarroController::class);
Route::apiResource('locacao', LocacaoController::class);
Route::apiResource('marca', MarcaController::class);
Route::apiResource('modelo', ModeloController::class);
