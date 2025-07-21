<?php

namespace App\Models;

use App\Traits\CodeTrait;
use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;
use App\Models\Log as Logs;

class VendorProfile extends Model
{
    use MongodbDataTableTrait, HasFactory, SoftDeletes, CodeTrait;

    protected $collection = 'vendor_profiles';
    protected $dates = ['deleted_at'];
    protected $guarded = [
        'id'
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->code = ($model::withTrashed()->count() + 1);
        });
        
        static::created(function ($item) {
            Logs::create([
                'Description' => 'Vendor Profile Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\VendorProfile',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Vendor Profile Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\VendorProfile',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Vendor Profile Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\VendorProfile',
            ]);
        });
    }
}
