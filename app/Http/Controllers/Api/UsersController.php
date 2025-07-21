<?php

namespace App\Http\Controllers\Api;

use App\Models\AreaOffice;
use App\Models\CollectionPoint;
use App\Traits\HttpResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\UserOtp;
use Carbon\Carbon;

class UsersController extends Controller
{
    use HttpResponseTrait;
    /**Login API
     * @param Request $request
     * @return array
     */
    public function login(Request $request)
    {
        // validate all required fields
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string|between:8,20',
        ]);
        // if issue in validation stop here and send failed response
        if ($validator->fails()) {
            $errors = $validator->errors()->messages();
            if(isset($errors['password']))
            $error = $errors['password'][0];
            elseif(isset($errors['email']))
            $error = $errors['email'][0];
            return $this->response(null, false,$error);
        }

        $auth = $request->all();
        $auth['status'] = 1;
        $fieldType = filter_var($auth['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'user_name';
        $user = User::where($fieldType, 'like', $auth['email'])->where('status', $auth['status'])->get()->first();
        if ($user == null)
            return $this->response(null, false, 'User not found');
        if ($user == null)
            return $this->response(null, false, 'User is not activated!');

        if (Hash::check($auth['password'], $user->password)) {
            $permissions = $user->roles->first()->permissions->sortBy('order')->pluck('name')->toArray();
            if ($user->mobile_user_only == 0 && $user->roles->first()->hasAnyPermission(['Milk Base Price']) == false)
                return $this->response(null, false, 'You dont have permission to use App');

            $user->tokens()->delete();
            $token = $user->createToken($user->user_name, $permissions)->plainTextToken;

            //access levels are: 1=collection point 2=area office 3=zones 4=sections 5=departments 6=plant
            $access_level = $user->roles->first()->access_level;
            $cp_code = $cp_name = $area_office = $ao_code = $mcc_id = $ao_id = '';
            if ($access_level == 1) {
                $cp = CollectionPoint::where('_id', $user['access_level_ids'][0])->with('area_office')->first();
                if ($cp) {
                    $cp_code = $cp->code;
                    $mcc_id = $cp->id;
                    $cp_name = $cp->name;
                    $area_office = $cp->area_office->name;
                }
            } else if ($access_level == 2) {
                $ao = AreaOffice::where('_id', $user['access_level_ids'][0])->first();
                if ($ao) {
                    $ao_code = $ao->code;
                    $area_office = $ao->name;
                    $ao_id = $ao->id;
                }
            }
            $trackingNo = null;
            if (isset($user->tracking_no)) {
                $trackingNo = $user->tracking_no;
            }


            $data = [
                'token' => $token,
                'isApproval' => $user->roles->first()->hasAnyPermission(['Milk Base Price']),
                'role' => $user->roles->first()->name,
                'permissions' => $permissions,
                'id' => $user->id,
                'mccCode' => $cp_code,
                'mccName' => $cp_name,
                'mccIid' => $mcc_id,
                'areaOfficeCode' => $ao_code,
                'areaOfficeName' => $area_office,
                'areaOfficeId' => $ao_id,
                'trackingNumber' => $trackingNo,
            ];

            return $this->response($data, true, 'Login Successfully');
        } else {
            return $this->response(null, false, 'Wrong password');
        }
    }

    public function userDetail()
    {
        $user = auth()->user('api');
        $permissions = $user->roles->first()->permissions->sortBy('order')->pluck('name')->toArray();
        //access levels are: 1=collection point 2=area office 3=zones 4=sections 5=departments 6=plant
        $access_level = $user->roles->first()->access_level;
        $cp_code = '';
        $cp_name = '';
        $area_office = '';
        if ($access_level == 1) {
            $cp = CollectionPoint::where('_id', $user['access_level_ids'][0])->with('area_office')->first();
            if ($cp) {
                $cp_code = $cp->code;
                $cp_name = $cp->name;
                $area_office = $cp->area_office->name;
            }
        }
        $data = [
            'isApproval' => $user->roles->first()->hasAnyPermission(['Milk Base Price']),
            'role' => $user->roles->first()->name,
            'permissions' => $permissions,
            'id' => $user->id,
            'mccCode' => $cp_code,
            'mccName' => $cp_name,
            'areaOffice' => $area_office
        ];
        return $this->response($data);
    }

    public function sendResetpasswordOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|exists:users,phone'
        ]);
        if ($validator->fails()) {
            return $this->response(null, false, $validator->errors());
        }

        $userOtp = $this->generateOtp($request->phone);
        $userOtp->sendSMS($request->phone);
        $data = [
            'user_id' => $userOtp->user_id,
        ];

        return $this->response($data, true, 'OTP has been sent on Your Mobile Number.');
    }

    public function generateOtp($phone)
    {

        $user = User::where('phone', $phone)->first();
        $userOtp = UserOtp::where('user_id', $user->id)->latest()->first();
        $now = now();

        if ($userOtp && $now->isBefore($userOtp->expire_at)) {
            return $userOtp;
        }

        return UserOtp::create([
            'user_id' => $user->id,
            'otp' => rand(123456, 999999),
            'expire_at' => Carbon::now()->addMinutes(10)
        ]);
    }

    public function resetPasswordWithOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,_id',
            'otp' => 'required',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        if ($validator->fails()) {
            return $this->fail($validator->errors());
        }

        $userOtp = UserOtp::where('user_id', $request->user_id)->where('otp', (int)$request->otp)->first();

        $now = now();
        if (!$userOtp) {
            return $this->response('', false, 'Your OTP is not correct');
        } else if ($userOtp && $now->isAfter($userOtp->expire_at)) {
            return $this->response('', false, 'Your OTP has been expired');
        }

        $user = User::where('_id', $request->user_id)->first();

        if ($user) {
            $userOtp->update([
                'expire_at' => Carbon::now()
            ]);

            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return $this->response('', true, 'Password Reset Successfully');
        }

        return $this->response('', false, 'Your Otp is not correct');
    }

    public function changePassword(Request $request)
    {
        $input = $request->all();
        // validate all required fields
        $validator = Validator::make($request->all(), [
            '_id' => 'required|string',
            'old_password' => 'required|string|between:8,20',
            'password' => 'required|string|between:8,20',
        ]);
        // if issue in validation stop here and send failed response
        if ($validator->fails()) {
            return $this->response(null, false, 'Change Password is not successful, Please try again.');
        }
        $user = User::find($input['_id']);
        if ($user == null)
            return $this->response('', false, "User does not exist!");

        if (Hash::check($input['old_password'], $user->password)) {
            $data['password'] = Hash::make($input['password']);
            $res = $user->update($data);
            if ($res)
                return $this->response(null, true, 'Password changed successfully');
            else
                return $this->response(null, false, 'Password is not changed successfully');
        } else {
            return $this->response(null, false, 'Old password is incorrect');
        }
    }

    public function logout()
    {
        $user = auth()->user('api');
        $user->tokens()->delete();
        return $this->response('', true, 'Logout Successfully');
    }
}
