<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request) 
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            unset($user['password']);
            $role = strtolower($user->role->name);
            $token = $user->createToken('spa', ["app:$role"])->plainTextToken;
            $request->session()->regenerate();

            $child = null;
            $subject = null;

            if($user['role_id']==2){
                $child = User::where('parent_id', $user->id)->get();;
            } elseif ($user['role_id'] == 3) {
                $subject = Subject::where('teacher_id', $user->id)->get();
            }

            return response()->json([
                'user' => $user,
                'child' => $child,
                'subject' => $subject,
                'token' => $token,
            ])->withCookie('token', $token, config('sanctum.expiration'), null, null, false, true);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);

    }

    public function logout(Request $request) 
    {
        if (auth()->user()->tokens()->delete()) {
            $request->session()->invalidate();
            $request->session()->regenerate();

            return response()->json(['message' => 'Logout'], 200);
        }
        return response()->json(['message' => 'Greska'], 200);

    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        try {
            if ($user = User::create($data)) {
                return response()->json([
                    'message' => "User created successfully.",
                    'data' => new UserResource($user)
                ]);
            }
        } catch (\Throwable $th) {
            Log::error('An error occured AuthController@register --> ' . $th->getMessage());
            
            return response()->json([
                'message' => 'There was an error.'
            ], 500);
        }

        
    }
}
