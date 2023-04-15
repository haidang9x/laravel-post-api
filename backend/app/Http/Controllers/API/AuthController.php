<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $all = $request->all();
        $validator = Validator::make($all, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:5'
        ]);
        if ($validator->fails()) return response()->json($validator->errors());
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(
            ['data' => $user, 'login_token' => $token, 'token_type' => 'Bearer',]
        );
    }
    ///login
    public function login(Request $request)
    {
        if (!$token = Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $user = User::where('email', $request['email'])->firstOrFail();
        $jwtToken = $this->createNewToken($token);
        $loginToken = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'You have loged! Nice day!',
            'access_token' => $jwtToken, 'token_type' => 'Bearer',
            'login_token' => $loginToken
        ]);
    }
    //logout
    public function logout()
    {
        auth()->user()->tokens()->delete();
        auth()->logout();
        return ['message' => 'you have loged out!'];
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}//class