<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\UserController;
use App\Http\Controllers\API\Auth\VerificationController;
use App\Http\Controllers\API\ConferenceController;
use App\Http\Controllers\API\FormController;
use App\Http\Controllers\API\ReviewerConferenceController;
use App\Http\Controllers\API\TopicController;
use App\Http\Controllers\API\PaperCallController;
use App\Http\Controllers\API\PaperController; 
use App\Http\Controllers\API\AssignPaperController;
use App\Http\Controllers\API\ConferenceScheduleController;
use App\Http\Controllers\API\SessionController;


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


Route::any('/verification/verify', [VerificationController::class, 'verify'])->name('api.verification.verify');

Route::get('/all-conferences', [ConferenceController::class,'index']);
Route::put('/invitation/{invitationId}/accept', [ReviewerConferenceController::class,'acceptInvitation']);
Route::put('/invitation/{id}/decline', [ReviewerConferenceController::class,'declineInvitation'])->name('invitation.decline');
Route::get('/topics-subtopics/{conference_id}', [TopicController::class, 'getTopicsAndSubtopic']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/users', [UserController::class,'index']);
    Route::get('/users/{id}', [UserController::class,'show']);
    Route::get('/usersSearch', [UserController::class,'searchUser']);
    Route::put('/update-user/{userId}', [UserController::class,'update']);
    Route::get('/users/{userId}/conference-attendance',  [UserController::class,'conferenceAttendance']);




    Route::get('/conference/{id}', [ConferenceController::class ,'show']);
    Route::post('/update/conference/{id}', [ConferenceController::class ,'update']);
    Route::put('/conference/{conferenceId}/topic/{topicId}', [ConferenceController::class ,'ConfereceTopic']);
    Route::post('/conferencesrequest', [ConferenceController::class, 'create']);
    Route::put('/conference/confirm/{conferenceId}', [ConferenceController::class ,'Confirmation']);

    Route::get('/conferences/not-accepted',  [ConferenceController::class, 'notAccepted']);
    Route::put('/conferences/{id}/accept', [ConferenceController::class ,'accept']);
    Route::put('/conferences/{id}/reject', [ConferenceController::class ,'reject']);
    Route::post('/conference/{id}/acceptationsSetting', [ConferenceController::class, 'acceptationsSetting']);
    Route::get('/conference/{id}/acceptanceInfo',[ConferenceController::class, 'getAcceptanceInfo']);

    Route::get('/user/conferences', [ConferenceController::class,'userConferences']);
    Route::get('/conference/{id}/topics', [TopicController::class, 'getTopicsAndSubtopic']);
    Route::delete('/conference/{conferenceId}/topic/{topicId}',[TopicController::class, 'deleteTopic']);

    Route::post('/invite-author', [PaperCallController::class,'sendEmails']);



    Route::post('/inviteReviewers', [ReviewerConferenceController::class,'inviteReviewers']);
    Route::get('/showInvitations/{conferenceId}', [ReviewerConferenceController::class,'showInvitations']);
    Route::post('/submit-paper', [PaperController::class,'submitPaper']);
    Route::get('/paper-details/{paperId}', [PaperController::class, 'show']);
    Route::get('/papers/{paperId}/average-score', [PaperController::class, 'calculateAverageScore']);
    Route::get('/papersAuthor/{conferenceId}/{userId}', [PaperController::class, 'getPaperForAuthor']);
    Route::get('/paper/uploadFinalVersion/{paperId}', [PaperController::class, 'getPaperForAuthor']);
    Route::get('/auto-assign/{conferenceId}', [AssignPaperController::class, 'autoAssign']);
    Route::get('/reviewer/assignedPapers/{conferenceId}', [AssignPaperController::class, 'getAssignedPapers']);

    Route::get('/reviewers/{conferenceId}', [AssignPaperController::class, 'getReviewers']);
    Route::get('/papers/{conferenceId}', [PaperController::class, 'getPapersByConference']);
    Route::get('/papers/{paperId}/download', [PaperController::class, 'download']);
    Route::post('/try-papers/{conferenceId}',  [PaperController::class,'tryPaper']);
    Route::post('/testPython/{conferenceId}',  [PaperController::class,'sessionCollect']);
    Route::post('/testAssignSlot/{conferenceId}',  [SessionController::class,'assign_slot']);

    Route::get('/fetchPaperTitles/{conferenceId}', [SessionController::class, 'fetchPaperTitles']);
    Route::get('/fetchSessionsWithSchedule/{conferenceId}', [SessionController::class, 'fetchSessionsWithSchedule']);


    Route::post('/saveForm',[FormController::class,'saveForm']);
    Route::get('/getFormForReview/{conferenceId}',[FormController::class,'getFormForReview']);
    Route::post('/storeEvaluation/{paperId}',[AssignPaperController::class,'storeEvaluation']);
    Route::get('/organize-sessions', [SessionController::class, 'organizeSessions']);
    Route::get('/sessions/{session}/papers', [SessionController::class, 'getPapersForSession']);
    Route::get('/conferences/{conferenceId}/schedules', [ConferenceScheduleController::class, 'showByConference']);
    Route::post('/conferencesSchedule', [ConferenceScheduleController::class, 'store']);


    Route::post('/logout', [UserController::class,'logout']);


});
