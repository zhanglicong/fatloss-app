<?php

use app\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DietPlanController;
use App\Http\Controllers\Api\WorkoutPlanController;
use App\Http\Controllers\UserController;
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


Route::post('/auth/register',[AuthController::class,'register']);
Route::post('/auth/login',[AuthController::class,'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'profile']);
    Route::post('/user/update', [UserController::class, 'updateProfile']);
    Route::post('/logout', [UserController::class, 'logout']);

    // 健身计划
    Route::get('/workout-plans', [WorkoutPlanController::class, 'index']);
    Route::post('/workout-plans', [WorkoutPlanController::class, 'store']);
    Route::get('/workout-plans/{id}', [WorkoutPlanController::class, 'show']);
    Route::put('/workout-plans/{id}', [WorkoutPlanController::class, 'update']);
    Route::delete('/workout-plans/{id}', [WorkoutPlanController::class, 'destroy']);

    // 饮食计划
    Route::get('/diet-plans', [DietPlanController::class, 'index']);
    Route::post('/diet-plans', [DietPlanController::class, 'store']);
    Route::get('/diet-plans/{id}', [DietPlanController::class, 'show']);
    Route::put('/diet-plans/{id}', [DietPlanController::class, 'update']);
    Route::delete('/diet-plans/{id}', [DietPlanController::class, 'destroy']);
});
