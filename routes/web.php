<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\VerificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::any('/verification/verify', [VerificationController::class, 'verify'])->name('verification.verify');

Route::get('/email/verify', [VerificationController::class, 'showVerificationPage'])->name('verification.verify');
Route::get('/email/verification/success', function () {
    return view('emails.verify-email');
})->name('verification.success');
Route::get('/email/verification/success', function () {
    return view('emails.verify-email');
})->name('verification.failed');
