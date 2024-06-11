<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL; // Import URL facade
use App\Models\User;

class VerificationController extends Controller
{
    public function verify(Request $request)
{
    // Validate the signed URL
     if (!URL::hasValidSignature($request)) {
        return redirect()->route('verification.failed')->with('status', 'Invalid verification link');
    }

    // Retrieve the user using the authenticated route
    $user = User::findOrFail($request->id);

    // Generate hash of user's email and compare with hash in request
    $expectedHash = sha1($user->email);
    if ($expectedHash !== $request->hash) {
        return redirect()->route('verification.failed')->with('status', 'Invalid verification link');
    }

    // Mark the email as verified and update the email_verified_at timestamp
    $user->markEmailAsVerified();
    $user->email_verified_at = now();
    $user->save();

    return redirect()->route('verification.success')->with('status', 'Email verified successfully');
}
}
