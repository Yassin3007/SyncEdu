<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubjectController;
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

//subject routes
Route::get('/subjects', [SubjectController::class, 'index']);
Route::post('/subjects', [SubjectController::class, 'store']);
Route::post('/subjects/{subject}', [SubjectController::class, 'update']);
Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy']);

// general routes
Route::get('/stages', [HomeController::class, 'getStages']);
Route::get('/grades/{id}', [HomeController::class, 'getGrades']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});
