<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TeacherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']);

//student routes
Route::get('/students', [StudentController::class, 'index']);
Route::post('/students', [StudentController::class, 'store']);
Route::post('/students/{student}', [StudentController::class, 'update']);
Route::delete('/students/{student}', [StudentController::class, 'destroy']);

//teacher routes
Route::get('/teachers', [TeacherController::class, 'index']);
Route::post('/teachers', [TeacherController::class, 'store']);
Route::post('/teachers/{teacher}', [TeacherController::class, 'update']);
Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});
