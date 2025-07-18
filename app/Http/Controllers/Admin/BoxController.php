<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Admin\Boxes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class BoxController extends Controller
{

    public function createBox(Request $request)
    {
        $validation= $request->validate([

            'user_id'=> 'int|required',
            'serial_id'=>'string|required', 
            // 'bluetooth_name'=> 'string|required',
            'variant_id'=> 'int|required',
        ]);

        $user_id= $request->user_id;
        $serial_id= $request->serial_id;
        // $bluetooth_name= $request->bluetooth_name;
        $variant_id= $request->variant_id;

        $result= $this->makeBox($user_id, $serial_id, $variant_id);
        

        if($result==true){

            return response()->json([
                'isSuccessful' => 'Yes',
                'statusCode'=>'200', 
                'errorMessage' => '',
                'responseMessage' => 'Box Successfully created',
            ]);
        }
        else{

            return response()->json([
                'isSuccessful' => 'No',
                'statusCode'=>'200', 
                'errorMessage' => 'Unable to create box in DB',
                'responseMessage' => 'Something went wrong. Please try again.',
            ]);
        }
        
    }


    public function sendBoxActivationCode(Request $request){



        $validation= $request->validate([

            'serial_id'=>'string|required',
            'email' => 'required|email',
            'region' => 'required',
        ]);


        $box= Boxes::where('serial_id', $request->serial_id)->first();


        if($box){

            if($box->is_active == 1){

                return response()->json([

                    'isSuccessful' => 'No',
                    'statusCode'=>'200', 
                    'errorMessage' => 'Box already active',
                    'responseMessage' => 'Box already active',
                ]);
            }

            //generate code and email this user

            $email = $request->email;
            $activation_code = Str::random(4);

            $box->activation_code = $activation_code;
            $box->region = $request->region;
            $box->save();


            $sent = $this->sendBoxActivationMail($email, $activation_code);
            

            if($sent == true){

                return response()->json([

                    'isSuccessful' => 'Yes',
                    'statusCode'=>'200', 
                    'errorMessage' => '',
                    'brainbox_serial_id' => $request->serial_id,
                    'region' => $request->region,
                    'responseMessage' => 'Mail for Brainbox Activation sent succesfully. Check your inbox !',

                ]);
            }
            else{

                return response()->json([

                    'isSuccessful' => 'No',
                    'statusCode'=>'200', 
                    'errorMessage' => 'Unable to send activation mail',
                    'responseMessage' => 'Something went wrong',
                ]);

            }
        }
        else{

            return response()->json([
                'isSuccessful' => 'No',
                'statusCode'=>'200', 
                'errorMessage' => 'Box not found',
                'responseMessage' => 'Box not found',
            ]);
        }
    }


    public function sendBoxActivationMail($email, $code){

        try{

            $path = URL::to('/');

            $to_email = $email;

            $data = array('email'=>$email, 'homepagelink'=>$path, 'code'=>$code);
            Mail::send('sendactivationcode', $data, function($message) use ($to_email) {

            $message->to($to_email)->subject

                ('Brainbox Customer App - Activate Box');

                $message->from('info@exdnow.com','Brainbox');

            });

            return true;
        }
        catch(Exception $e){

            return false;
    
        }
    }

    
    public function makeBox($user_id, $serial_id, $variant_id){


        $box = new Boxes();

        $box->serial_id = $serial_id;
        // $box->lat_long = "N/A";
        // $box->ssid = "N/A";
        // $box->password = "N/A";
        // $box->bluetooth_name = $bluetooth_name;
        $box->boxable_id = $variant_id;
        $box->boxable_type = 'xyz';
        $box->cloud_id = 1; //cloud id is 1 always.
        $box->created_by= $user_id;
        $box->last_updated_by= $user_id;

        $flag1=$box->save();

        if( $flag1==true){

            return true;
        }
        else{

            return false;
        }
    }

}
