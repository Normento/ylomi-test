<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [RegisteredUserController::class, 'register']);

// Route pour la connexion
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Route pour la déconnexion (protégée par authentification)
Route::middleware('auth:sanctum')->post('/logout', [AuthenticatedSessionController::class, 'logout']);


Route::post('/verify-code', [VerificationController::class, 'checkcode']);

Route::middleware('auth:sanctum')->post('/bank-accounts', [BankAccountController::class, 'store']);


Route::middleware('auth:sanctum')->post('/bank-accounts/{accountId}/deposit', [BankAccountController::class, 'deposit']);

Route::middleware('auth:sanctum')->post('/bank-accounts/{accountId}/withdraw', [BankAccountController::class, 'withdraw']);

Route::middleware('auth:sanctum')->get('/bank-accounts/{accountId}/transactions', [BankAccountController::class, 'transactions']);

Route::middleware(['auth:sanctum', 'can:viewAny,App\Models\User'])->get('/users', [UserController::class, 'index']);

Route::middleware(['auth:sanctum', 'can:update,App\Models\User'])->put('/users/{userId}', [UserController::class, 'update']);

Route::middleware(['auth:sanctum', 'can:delete,App\Models\User'])->delete('/users/{userId}', [UserController::class, 'destroy']);

Route::middleware(['auth:sanctum', 'can:deactivate,App\Models\BankAccount'])->put('/bank-accounts/{accountId}/deactivate', [BankAccountController::class, 'deactivate']);

Route::middleware(['auth:sanctum', 'can:deactivate,App\Models\BankAccount'])->put('/bank-accounts/{accountId}/activate', [BankAccountController::class, 'activate']);

Route::middleware('auth:sanctum')->get('/bank-accounts/{accountId}', [BankAccountController::class, 'show']);

Route::middleware('auth:sanctum')->get('/bank-accounts', [BankAccountController::class, 'index']);

Route::middleware(['auth:sanctum', 'can:deactivate,App\Models\BankAccount'])->get('/bank-accounts/deactivated', [BankAccountController::class, 'listDeactivatedAccounts']);






