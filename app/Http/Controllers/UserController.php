<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'account created successfully'
        ]);;
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::whereEmail($request->email)->first();

        if(isset($user->id)){
            if(Hash::check($request->password, $user->password)){
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'message' => 'Connected successfully',
                    'token' => $token
                ]);
            }else{
                return response()->json([
                    'message' => 'password inncorect'
                ]);
            }
        }else{
            return response()->json([
                'message' => 'user not found'
            ]);
        }
    }

    public function profile(){
        return new UserResource(auth()->user());
    }

    public function logout(){
        Auth::auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logout succesfully'
        ]);
    }
}
