<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\UserController;
use App\Http\Controllers\API\Auth\EmailVerificationController;
use App\Http\Controllers\API\ConferenceController;
use App\Http\Controllers\API\FormController;
use App\Http\Controllers\API\ReviewerConferenceController;
use App\Http\Controllers\API\TopicController;

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
    Route::put('/invitation/{id}/accept', [ReviewerConferenceController::class,'acceptInvitation']);


});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/users', [UserController::class,'index']);
    Route::get('/users/{id}', [UserController::class,'show']);
    Route::get('/usersSearch', [UserController::class,'searchUser']);


    Route::get('/conference/{id}', [ConferenceController::class ,'show']);
    Route::post('/update/conference/{id}', [ConferenceController::class ,'update']);
    Route::post('/conference/{id}/topic', [ConferenceController::class ,'ConfereceTopic']);
    Route::get('/all-conferences', [ConferenceController::class,'index']);
    Route::post('/conferencesrequest', [ConferenceController::class, 'create']);
    Route::get('/conferences/not-accepted',  [ConferenceController::class, 'notAccepted']);
    Route::put('/conferences/{id}/accept', [ConferenceController::class ,'accept']);
    Route::put('/conferences/{id}/reject', [ConferenceController::class ,'reject']);
    Route::get('/user/conferences', [ConferenceController::class,'userConferences']);
    Route::get('/conference/{conference}/topics', [ConferenceController::class, 'topics']);
    Route::get('/topics/{topic}/subtopics', [TopicController::class, 'index']);

    Route::get('/form', [FormController::class,'store']);

    Route::post('/inviteReviewers', [ReviewerConferenceController::class,'inviteReviewers']);
    Route::post('/invitation/{id}/decline', [ReviewerConferenceController::class,'declineInvitation'])->name('invitation.decline');



});
