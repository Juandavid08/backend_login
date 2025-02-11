<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsuarioController;

Route::post("/login", [UsuarioController::class,'login']);

Route::post("/logout", [UsuarioController::class,'logout']);

Route::get('/users',[UsuarioController::class, 'index']);

Route::get('/user/{id}', [UsuarioController::class,'show']);

Route::post('/users', [UsuarioController::class, 'store']);

Route::delete('/user/{id}', [UsuarioController::class, 'delete']);

Route::patch('/user/{id}', [UsuarioController::class, 'update']);

