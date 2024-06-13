<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required',
                ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'statut' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors(),
                ], 401);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            return response()->json([
                'statut' => true,
                'message' => 'Utilisateur creer avec succès',
                'token' => $user->createToken('API TOKEN')->plainTextToken,
            ], 200);

        }catch(\Throwable $th) {
            return response()->json([
                'statut' => false,
                'message' => $th->getMessage(),
            ], 500);
        }

    }
     public function login(Request $request)
     {
        try
        {
            
            $validateUser = Validator::make($request->all(),
                [
                    
                    'email' => 'required|email',
                    'password' => 'required',
                ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'statut' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors(),
                ], 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))){

                return response()->json([
                    'statut' => false,
                    'message' => 'email & password does not match with our record',
                
                ], 401);
                
            }
            $user = User::where('email', $request->email)->first();
            return response()->json([
                'status' => true,
                'message' => 'User Logged In succefully',
                'token' => $user->createToken('API TOKEN')->plainTextToken
            ], 200);
            
        } catch(\Throwable $th) {
            return response()->json([
                'statut' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
     }
}