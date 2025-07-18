<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{

    public function index()
    {

        $users = User::get();
        //$users = User::get();

        return response()->json(['users' => $users], 200);
    }

    public function register(Request $request)
    {

        $credentials = $request->only('name', 'email', 'password','region');
        $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required',
            'region' => 'required',
        ];

        $validator = Validator::make($credentials, $rules);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->messages()]);
        } else {
            //check if user already exists
            $userExist = User::where('email', $request->email)->exists();

            if (!$userExist) {

                $user = new User();

                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = bcrypt($request->password);
                $user->region = $request->region;

                $user->save();

                $token = $user->createToken('authToken')->plainTextToken;


                return response()->json(['access_token' => $token, 'token_type' => 'Bearer'], 200);
            } else {
                return response()->json(['status' => false, 'error' => 'User already Exists !']);
            }
        }
    }
}
