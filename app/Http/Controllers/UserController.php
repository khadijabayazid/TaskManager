<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\json;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        return response()->json([
            'messager' => 'User Registered Successfully',
            'User' => $user,
        ], 201);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);
        if (!Auth::attempt($request->only('email', 'password')))
            return response()->json(
                [
                    'message' => 'invalid email or password'
                ],
                401
            );
        $user = User::where('email', $request->email)->FirstOrFail();
        $token = $user->createToken('auth_Token')->plainTextToken;
        return response()->json([
            'messager' => 'Login Successfully',
            'User' => $user,
            'Token' => $token
        ], 201);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'messager' => 'Logout Successfully',
        ]);
    }

    public function getProfile($id)
    {
        $user = User::findOrFail($id);
        if($user->id != Auth::user()->id)
            return response()->json(['message'=>'Unauthorized'], 403);
        $profile = $user->profile;
        return response()->json($profile, 200);
    }

    public function getUserTasks($id)
    {
        $user = User::findOrFail($id);
        if ($user->id != Auth::user()->id)
            return response()->json(['message' => 'Unauthorized'], 403);
        $tasks = $user->tasks;
        return response()->json($tasks, 200);
    }

    public function GetUser(){
        $user_id = Auth::user()->id;
        $userData = User::with('profile')->findOrFail($user_id);
        return new UserResource($userData);
    }
}
