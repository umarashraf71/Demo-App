<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Twilio\Rest\Client;
use Exception;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use App\Traits\CodeTrait;
use Auth;
use NsTechNs\JazzCMS\JazzCMS;

class UserOtp extends Model
{
    use MongodbDataTableTrait, HasFactory, SoftDeletes, CodeTrait;

    protected $collection = 'user_otps';

    protected $fillable = ['user_id', 'otp', 'expire_at'];
    protected $casts = ['expire_at' => 'datetime'];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->code = ($model::withTrashed()->count() + 1);
        });
        
        static::created(function ($item) {
            Logs::create([
                'Description' => 'User Otp Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\UserOtp',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'User Otp Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\UserOtp',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'User Otp Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\UserOtp',
            ]);
        });
    }

    public function sendSMS($receiverNumber)
    {
        $message = "Login OTP is " . $this->otp;
        $receiverNumber = str_replace('-', '', $receiverNumber);

        $response = (new JazzCMS)->sendSMS($receiverNumber, $message);

        return;
    }
}
