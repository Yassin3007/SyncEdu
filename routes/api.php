<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\LessonController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserRoleController;
use App\Http\Controllers\Mobile\StudentAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']);


//Route::group(['middleware' => 'auth:sanctum'], function () {
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
    Route::get('/generate-lessons/{teacher}', [LessonController::class, 'createLessonsForTeacher']);

//subject routes
    Route::get('/subjects', [SubjectController::class, 'index']);
    Route::post('/subjects', [SubjectController::class, 'store']);
    Route::post('/subjects/{subject}', [SubjectController::class, 'update']);
    Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy']);

//lesson routes
    Route::get('/tables', [TableController::class, 'index']);
    Route::get('/tables/{table}', [TableController::class, 'show']);
    Route::post('/tables', [TableController::class, 'store']);
    Route::post('/tables/{table}', [TableController::class, 'update']);
    Route::delete('/tables/{table}', [TableController::class, 'destroy']);

Route::prefix('lessons')->group(function () {
    // Standard CRUD routes
    Route::get('/', [LessonController::class, 'index']);
    Route::post('/', [LessonController::class, 'store']);
    Route::get('/{lesson}', [LessonController::class, 'show']);
    Route::post('/{lesson}', [LessonController::class, 'update']);
    Route::delete('/{lesson}', [LessonController::class, 'destroy']);

    // Additional custom routes
    Route::get('/teacher-schedule/date', [LessonController::class, 'getByTeacherAndDate']); // GET /api/lessons/teacher-schedule/date
    Route::get('/schedule/weekly', [LessonController::class, 'getWeeklySchedule']); // GET /api/lessons/schedule/weekly
});


// attendance routes
    Route::post('/attendance', [AttendanceController::class, 'store']);
    Route::post('/change-attendance-status', [AttendanceController::class, 'changeAttendanceStatus']);
    Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy']);

//});

// general routes
Route::get('/stages', [HomeController::class, 'getStages']);
Route::get('/grades/{id}', [HomeController::class, 'getGrades']);
Route::get('/divisions/{id}', [HomeController::class, 'getDivisions']);
Route::get('/districts', [DistrictController::class, 'index']);
Route::get('/districts/city/{city}', [DistrictController::class, 'getByCity']);


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
Route::get('permissions',[PermissionController::class, 'index']);
Route::get('permissions/{permission}',[PermissionController::class, 'show']);
Route::post('permissions',[PermissionController::class, 'store']);
Route::post('permissions/{permission}',[PermissionController::class, 'update']);
Route::delete('permissions/{permission}',[PermissionController::class, 'destroy']);
//Route::apiResource('permissions', PermissionController::class);

// User Role Management
Route::post('users/{user}/roles', [\App\Http\Controllers\API\UserRoleController::class, 'assignRoles']);
Route::get('users/{user}/roles', [\App\Http\Controllers\API\UserRoleController::class, 'getUserRolesAndPermissions']);
Route::delete('users/{user}/roles', [\App\Http\Controllers\API\UserRoleController::class, 'removeRole']);



// User CRUD routes
Route::middleware('auth:sanctum')->prefix('users')->group(function () {
    // CRUD operations
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::post('/', [UserController::class, 'store'])->name('users.store');
    Route::get('{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('{user}', [UserController::class, 'update'])->name('users.patch');
    Route::delete('{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Additional actions
    Route::patch('{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('{user}/send-reset-link', [UserController::class, 'sendPasswordResetLink'])->name('users.send-reset-link');
    Route::patch('{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('roles', [UserController::class, 'getRoles'])->name('users.roles');
});
//Route::get('users',[UserController::class, 'index']);
//Route::get('users/{user}',[UserController::class, 'show']);
//Route::post('users',[UserController::class, 'store']);
//Route::post('users/{user}',[UserController::class, 'update']);
//Route::delete('users/{user}',[UserController::class, 'destroy']);
//Route::apiResource('users', UserController::class);

// Additional user management routes
Route::put('users/{user}/activate', [UserController::class, 'activate'])
    ->name('users.activate');

// Force delete - only for admin users with special permission
Route::delete('users/{user}/force', [UserController::class, 'forceDestroy'])
    ->name('users.force-destroy')
    ->middleware('permission:force-delete-users');
















    // students routes

Route::prefix('student')->group(function () {
    // Public routes (no authentication required)
    Route::post('/register', [StudentAuthController::class, 'register']);
    Route::post('/login', [StudentAuthController::class, 'login']);
    Route::post('/request-password-reset', [StudentAuthController::class, 'requestPasswordReset']);
    Route::post('/verify-code', [StudentAuthController::class, 'verifyCodeAndLogin']);

    // Protected routes (authentication required using Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/change-password', [StudentAuthController::class, 'changePassword']);
        Route::post('/reset-password', [StudentAuthController::class, 'resetPassword']);
        Route::post('/logout', [StudentAuthController::class, 'logout']);
    });
});
