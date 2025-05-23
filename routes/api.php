<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserRoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']);

//student routes
Route::get('/students', [StudentController::class, 'index']);
Route::get('/students/{student}', [StudentController::class, 'show']);
Route::post('/students', [StudentController::class, 'store']);
Route::post('/students/{student}', [StudentController::class, 'update']);
Route::delete('/students/{student}', [StudentController::class, 'destroy']);
Route::post('/students/bulk/move', [StudentController::class, 'bulkMove']);

//teacher routes
Route::get('/teachers', [TeacherController::class, 'index']);
Route::post('/teachers', [TeacherController::class, 'store']);
Route::post('/teachers/{teacher}', [TeacherController::class, 'update']);
Route::get('/teachers/{teacher}', [TeacherController::class, 'show']);
Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy']);

//subject routes
Route::get('/subjects', [SubjectController::class, 'index']);
Route::post('/subjects', [SubjectController::class, 'store']);
Route::post('/subjects/{subject}', [SubjectController::class, 'update']);
Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy']);

//lesson routes
Route::get('/lessons', [LessonController::class, 'index']);
Route::get('/lessons/{lesson}', [LessonController::class, 'show']);
Route::post('/lessons', [LessonController::class, 'store']);
Route::post('/lessons/{lesson}', [LessonController::class, 'update']);
Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy']);

// general routes
Route::get('/stages', [HomeController::class, 'getStages']);
Route::get('/grades/{id}', [HomeController::class, 'getGrades']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});

















// Roles API endpoints

Route::get('roles',[RoleController::class, 'index']);
Route::get('roles/{role}',[RoleController::class, 'show']);
Route::post('roles',[RoleController::class, 'store']);
Route::post('roles/{role}',[RoleController::class, 'update']);
Route::delete('roles/{role}',[RoleController::class, 'destroy']);
//Route::apiResource('roles', RoleController::class);

// Permissions API endpoints
Route::apiResource('permissions', PermissionController::class);

// User Role Management
Route::post('users/{user}/roles', [\App\Http\Controllers\API\UserRoleController::class, 'assignRoles']);
Route::get('users/{user}/roles', [\App\Http\Controllers\API\UserRoleController::class, 'getUserRolesAndPermissions']);
Route::delete('users/{user}/roles', [\App\Http\Controllers\API\UserRoleController::class, 'removeRole']);



// User CRUD routes
Route::apiResource('users', UserController::class);

// Additional user management routes
Route::put('users/{user}/activate', [UserController::class, 'activate'])
    ->name('users.activate');

// Force delete - only for admin users with special permission
Route::delete('users/{user}/force', [UserController::class, 'forceDestroy'])
    ->name('users.force-destroy')
    ->middleware('permission:force-delete-users');
