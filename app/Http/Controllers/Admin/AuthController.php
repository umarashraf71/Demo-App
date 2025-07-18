<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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

        $credentials = $request->only('name', 'email', 'password');
        $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required',
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

                $user->save();

                $token = $user->createToken('authToken')->plainTextToken;


                return response()->json(['access_token' => $token, 'token_type' => 'Bearer'], 200);
            } else {
                return response()->json(['status' => false, 'error' => 'User already Exists !']);
            }
        }
    }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $validator = Validator::make($credentials, $rules);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->messages()]);
        }


        $user_exists= User::where('email', $request->email)->first();


        if ($user_exists)
        {
            
            $passwordCheck = false;
            if(Hash::check($request->password, $user_exists->password)) {
                $passwordCheck = true;
            }

            if($passwordCheck)
            {
                $token = $user_exists->createToken('authToken')->plainTextToken;
                return response()->json(['token' => $token], 200);
            }
            else
            {
                return response()->json(['error' => 'Incorrect Password'], 401);
            }
        }
        else
        {
            return response()->json(['error' => 'User dont exists'], 401);
        }
    }
}
