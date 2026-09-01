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
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|exists:groups,id',
            'name' => 'required|min:5',
            'username' => 'required|min:3|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->json_response('error', 'Validation failed', $validator->errors(), 422);
        }
        $user = new User();
        $user->group_id = $request->group_id;
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        return $this->json_response('success', 'Register', 'User Register Successfully', 200, $user->toArray());
    }

    public function authenticate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->json_response('error', 'Validation failed', $validator->errors(), 422);
        }

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $user = User::find($user = Auth::id());
            $token = $user->createToken('token')->plainTextToken;
            return $this->json_response('success', 'Login', 'Login Account Successfully', 200, $user->toArray(), $token);
        } else {
                return $this->json_response('error', 'Validation failed', 'Either Username/Password is incorrect', 401);
        }
    }
}