<?php

namespace App\Http\Controllers\Apis;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'group_id' => 'required|exists:groups,id',
                'name' => 'nullable|min:5',
                'username' => 'required|min:3|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->json_response('error', 'Validation failed', $validator->errors(), 422);
            }
            $user = new User;
            $user->group_id = $request->group_id;
            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            return $this->json_response('success', 'Register', 'User Register Successfully', 200, $user);
        } catch (\Exception $e) {
            return $this->json_response('error', 'Register Failed', 'Something went wrong: '.$e->getMessage(), 500);
        }
    }

    public function authenticate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->json_response('error', 'Validation failed', $validator->errors(), 422);
            }

            if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
                $user = User::find(Auth::id());

                $user->tokens()->delete();
                $tokenData = $this->issueTokens($user);

                return $this->json_response('success', 'Login', 'Login Account Successfully', 200, $user, $tokenData);
            } else {
                return $this->json_response('error', 'Validation failed', 'Either Username/Password is incorrect', 401);
            }
        } catch (\Exception $e) {
            return $this->json_response('error', 'Login Failed', 'Something went wrong: '.$e->getMessage(), 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            $user->tokens()->delete();

            return $this->json_response('success', 'Logout', 'Logged out successfully', 200);
        } catch (\Exception $e) {
            return $this->json_response('error', 'Logout Failed', 'Something went wrong: '.$e->getMessage(), 500);
        }
    }

    private function issueTokens(User $user): array
    {
        $accessTtl = $this->accessTokenTtl();
        $accessExpiresAt = now()->addMinutes($accessTtl);

        $accessToken = $user->createToken('access_token', ['*'], $accessExpiresAt);

        return [
            'access_token' => $accessToken->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $accessExpiresAt->toDateTimeString(),
            'expires_in' => $accessTtl * 60,
        ];
    }

    private function accessTokenTtl(): int
    {
        return (int) config('sanctum.access_token_expiration', 1440);
    }
}