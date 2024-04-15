<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\UserController;
use App\Http\Controllers\API\Auth\EmailVerificationController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
 

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

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::get('/verification/verify/{user}', function ($user) {
    $user = User::find($user);

    if (! $user || ! Hash::check(signedRoute('verification.verify', ['user' => $user->id]), now())) {
        return response()->json(['message' => 'Invalid verification link.'], 404);
    }

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'You have already verified your email address.'], 400);
    }

    $user->markEmailAsVerified();

    return response()->json(['message' => 'Your email address has been verified.'], 200);
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
