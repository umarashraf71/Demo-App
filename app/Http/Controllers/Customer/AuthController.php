<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

    
    public function index(Request $request)
    {

        return $request->user();
        // $users = User::get();
        // //$users = User::get();

        // return response()->json(['users' => $users], 200);
    }

    //get region from form and then put it in register api header and from header our middleware will switch its database
    public function register(Request $request)
    {

        $credentials = $request->only('name', 'email', 'password', 'country', 'region', 'address');
        $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required',
            'country' => 'required',
            'region' => 'required',
            'address' => 'required'
        ];

        $validator = Validator::make($credentials, $rules);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->messages()]);
        } else {

            $region = DB::connection('mongodbMaster')->table('regions')->where('region', $request->region)->first();

            if($region)
            {   
                $regionDb = $region['db'];
            
                DB::setDefaultConnection($regionDb);
                DB::reconnect($regionDb);
            }
            else
            {
                return response()->json(['status' => false, 'error' => 'Region dont exist !']);
            }

            //check if user already exists or not in master and region customer db
            $masterUserCheck = DB::connection('mongodbMaster')->table('users')->where('email', $request->email)->exists();
            $customerUserCheck = User::where('email', $request->email)->exists();


            if (!$masterUserCheck && !$customerUserCheck) {

                $name = $request->name;
                $email = $request->email;
                $country = $request->country;
                $region = $request->region;
                $address = $request->address;
                $password = bcrypt($request->password);

                //save user details in master customer DB
                // DB::connection('mysql')->beginTransaction();
                $masterUser = DB::connection('mongodbMaster')->table('users')->insert(['name' => $name,
                                                                 'email' => $email,
                                                                 'password' => $password,
                                                                 'country' => $country,
                                                                 'region' => $region,
                                                                 'address' => $address,
                                                                 'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                                                                 'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                                                                ]);
                // DB::connection('mysql')->commit();
                if($masterUser)
                {

                    //save user details in specific region db with respect to its region
                    $customerUser = new User();

                    $customerUser->name = $name;
                    $customerUser->email = $email;
                    $customerUser->country = $country;
                    $customerUser->region = $region;
                    $customerUser->address = $address;
                    $customerUser->password = $password;

                    $customerUser->save();

                    $token = $customerUser->createToken('authToken')->plainTextToken;

                    return response()->json(['access_token' => $token, 'token_type' => 'Bearer', 'region' => $region], 200);
                }
                else
                {
                    return response()->json(['status' => false, 'error' => 'Unable to Sign up in master DB !']);
                }

            } else {
                return response()->json(['status' => false, 'error' => 'User with this email already exists !']);
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

        //authenticate user details from master customer db
        $masterUser = DB::connection('mongodbMaster')->table('users')->where('email', $request->email)->first();
        
        if($masterUser)
        {
            $masterUserRegion = $masterUser['region'];
        
            $region = DB::connection('mongodbMaster')->table('regions')->where('region', $masterUserRegion)->first();
            
            if($region)
            {   
                $regionDb = $region['db'];
            
                DB::setDefaultConnection($regionDb);
                DB::reconnect($regionDb);
            }
            else
            {
                return response()->json(['error' => 'User Region dont Exist !'], 401);
            }
        }

        $customerUser = User::where('email', $request->email)->first();

        if($masterUser && $customerUser)
        {
            if(!Hash::check($request->password, $customerUser->password)) 
            {
                return response()->json(['error' => 'Incorrect Password'], 401);
            }

            $token = $customerUser->createToken('authToken')->plainTextToken;
            return response()->json(['token'=> $token, 'region' => $customerUser->region, 'user' => $customerUser], 200);
        }
        else
        {
            return response()->json(['error' => 'User dont exists'], 401);
        }
    }



    public function forgotPassword(Request $request)
    {
        $validator =  $request->validate([
            'email'=>'email|required',
        ]);

        //authenticate user details from master customer db
        $masterUser = DB::connection('mongodbMaster')->table('users')->where('email', $request->email)->first();
        
        if($masterUser)
        {
            $masterUserRegion = $masterUser['region'];
        
            $region = DB::connection('mongodbMaster')->table('regions')->where('region', $masterUserRegion)->first();
            
            if($region)
            {   
                $regionDb = $region['db'];
            
                DB::setDefaultConnection($regionDb);
                DB::reconnect($regionDb);
            }
            else
            {
                return response()->json(['error' => 'User Region dont Exist !'], 401);
            }
        }

        $customerUser = User::where('email', $request->email)->first();

        if($masterUser && $customerUser)
        {
            $resetToken = Str::random(32);

            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $resetToken,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);

            if($this->sendResetEmail($request->email, $resetToken)) 
            {
                return response()->json([
                    'isSuccessful' => 'Yes',
                    'errorMessage' => '',
                    'responseMessage' => 'An Email Has Been Sent With Reset Link.',
                ]);
            } 
            else 
            {
                return response()->json([
                    'isSuccessful' => 'No',
                    'errorMessage' => 'Something went wrong. Could not send reset email',
                    'responseMessage' => 'Request Failed',
                ]);
            }
        }
        else
        {
            return response()->json([
                'isSuccessful' => 'No',
                'errorMessage' => 'Invalid User Email',
                'responseMessage' => 'Request Not Successful, Please try again.',
            ]);
        }
    }


    public function sendResetEmail($email, $token)
    {

        try
        {
            $path = URL::to('/');
            $link = $path . '/reset-password?token=' . $token . '&email=' . urlencode($email);
            $to_email = $email;

            $data = array('homepagelink'=>$path, 'resetlink'=>$link);
            $mail = Mail::send('resetlinkmail', $data, function($message) use ($to_email) {
            $message->to($to_email)->subject
                ('Brainbox Customer - Reset Password');
            $message->from('info@exdnow.com','Brainbox');
            });

            return true;
        }
        catch(\Swift_TransportException $transportExp){
        
            $transportExp->getMessage();
            return false;  
        }
    }


    public function resetPassword( Request $request ) {

        // validate user_email.
        $validator =  $request->validate([
            'email'=>'email|required',
            'reset_token'  => 'required|string',
            'new_password' => 'required|string',
        ]);


        $user_token = $request->reset_token;
        $new_password = $request->new_password;
    
         //authenticate user details from master customer db
         $masterUser = DB::connection('mongodbMaster')->table('users')->where('email', $request->email)->first();
        
         if($masterUser)
         {
             $masterUserRegion = $masterUser['region'];
         
             $region = DB::connection('mongodbMaster')->table('regions')->where('region', $masterUserRegion)->first();
             
             if($region)
             {   
                 $regionDb = $region['db'];
             
                 DB::setDefaultConnection($regionDb);
                 DB::reconnect($regionDb);
             }
             else
             {
                 return response()->json(['error' => 'User Region dont Exist !'], 401);
             }
         }
 
         $customerUser = User::where('email', $request->email)->first();
 
         if($masterUser && $customerUser)
         {
            //validate token
            $tokenData = DB::table('password_resets')->where('token', $user_token)->first();

            if($tokenData)
            {
                //update in customer db
                $newPassword = bcrypt($new_password);
                $customerUser->password = $newPassword;
                $result = $customerUser->save();

                //update in master db
                $masterUserPassword = DB::connection('mongodbMaster')->table('users')->where('email',$request->email)->update([
                'password' => $newPassword,
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
               ]);

               //if updated in both db
               if($result && $masterUserPassword)
               {
                    //delete used token
                    DB::table('password_resets')->where('email', $customerUser->email)->delete();

                    return response()->json([
                        'isSuccessful' => 'Yes',
                        'errorMessage' => '',
                        'responseMessage' => 'Request Successful, Password has been changed.',
                    ]);
               }

            }
            else
            {
                return response()->json([
                    'isSuccessful' => 'No',
                    'errorMessage' => 'Invalid Token',
                    'responseMessage' => 'Request Not Successful, Please try again.',
                ]);
            }
         }
         else
         {
            return response()->json([
                'isSuccessful' => 'No',
                'errorMessage' => 'Invalid User Email',
                'responseMessage' => 'Request Not Successful, Please try again.',
            ]);
         }
    
    }

}
