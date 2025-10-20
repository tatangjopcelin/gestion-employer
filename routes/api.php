<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\ShiftController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class,'me']);
    Route::apiResource('plannings', PlanningController::class);

    Route::post('/shift/{shift}/pointage-debut', [ShiftController::class, 'pointageDebut']);
    Route::post('/shift/{shift}/pointage-fin', [ShiftController::class, 'pointageFin']);
    // Route::get('/plannings/rapport-mensuel/{mois}/{annee}', [PlanningController::class, 'rapportMensuel']);
});

Route::get('/mes-plannings', [PlanningController::class, 'mesPlannings']);
Route::get('/plannings/rapport-mensuel/{mois}/{annee}', [PlanningController::class, 'rapportMensuel']);


