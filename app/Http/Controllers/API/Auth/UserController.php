<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;



class UserController extends Controller {
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8', 
            'country' => 'required|string',
            'affiliation' => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }
        // Check if email already exists
    $existingUser = User::where('email', $request->email)->first();
    if ($existingUser) {
        return response()->json(['message' => 'Email already exists'], 409);
    }


        $user = User::create([
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'country' => $request->country,
            'affiliation' => $request->affiliation,
        ]);
        $user->save();
        $user->assignRole('user');
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->sendEmailVerificationNotification();
       
        return response()->json(['message' =>'User registered successfully. Please check your email to verify your account.', 'token' => $token, 'user' => $user], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $user = User::where('email', $request->input('email'))->first();
        
        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $roles = $user->roles()->pluck('name');

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['message' => 'Registered successfully', 'token' => $token, 'user' => $user,        'roles' => $roles,
    ], 200);

    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'string',
            'lastName' => 'string',
            'email' => 'string|email|unique:users,email,' . auth()->id(),
            'password' => 'string',
            'country' => 'string',
            'affiliation' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = auth()->user();
        $user->update($request->all());

        return response()->json(['message' =>'User Update successfully.', 'token' => $token, 'user' => $user], 200);
    }

    public function delete(Request $request)
    {
        $user = auth()->user();

        // Check if the authenticated user has the "admin" role
        if (!$user->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        // Proceed with user deletion
        $userToDelete = User::findOrFail($request->user_id);
        $userToDelete->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }
    public function index()
    {
        $user = User::all();

        return response()->json($user);
    }
    public function searchUser(Request $request)
{
    $email = $request->input('email');
    $user = User::where('email', $email)->first();

    if ($user) {
        return response()->json($user);
    } else {
        return response()->json(['message' => "User with email ".$email." was not found. Enter information below to continue. Account will not be created for the user."], 404);
    }
}
}
