<?php

namespace App\Http\Controllers\v1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'min:8', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'entity is unprocessable'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        User::create($request->only(['name', 'email', 'password']));
        return response([
            'message' => 'successfully registered'
        ], Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'min:8', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'entity is unprocessable'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $credentials = request(['email', 'password']);
        try {
            Auth::attempt($credentials);
            $user = User::where('email', $request->email)->first();
            $token_result = $user->createToken('auth-token')->plainTextToken;
            return response()->json(['message' => $token_result], Response::HTTP_OK);

        } catch (Throwable $th) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);

        }
        
        
    }
    public function logout(Request $request){
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Token deleted successfully'], Response::HTTP_OK);
        } catch (Throwable $th) {
            return response()->json(['message' => 'Unauthenticated'], Response::HTTP_UNAUTHORIZED);
        }

    }
}
