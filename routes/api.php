<?php

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
Route::apiResource('pacientes',               'API\PacientesController');
Route::apiResource('especificaiones',         'API\EspecificaionesController');
Route::apiResource('registros',               'API\RegistrosController');
Route::get('registros-get-num',               'API\RegistrosController@getNum');
Route::get('catalogos',                       'API\CatalogosController@catalogos');
Route::get('clientes',                        'API\CatalogosController@clientes');
Route::get('filtros',                         'API\CatalogosController@filtros');
Route::get('historial/{id}',                       'API\RegistrosController@historial');
//Route::get('/registros',               [RegistrosController::class, 'all']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
