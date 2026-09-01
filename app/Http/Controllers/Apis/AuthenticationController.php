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
                'name' => 'required|min:5',
                'email' => 'required|email|unique:users',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->json_response('error', 'Validation failed', $validator->errors(), 422);
            }
            $user = new User();
            $user->group_id = $request->group_id ?? 1;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            return $this->json_response('success', 'Register', 'User Register Successfully', 200, $user->toArray());
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }

    public function authenticate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->json_response('error', 'Validation failed', $validator->errors(), 422);
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = User::find($user = Auth::id());
                $token = $user->createToken('token')->plainTextToken;
                return $this->json_response('success', 'Login', 'Login Account Successfully', 200, $user->toArray(), $token);
            } else {
                return $this->json_response('error', 'Validation failed', 'Either Email/Password is incorrect', 401);
            }
        } catch (\Throwable $e) {
            return $this->json_response('error', 'Something went wrong', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()], 500);
        }
    }
}