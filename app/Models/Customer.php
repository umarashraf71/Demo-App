<?php

namespace App\Models;

use App\Traits\CodeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;

class Customer extends Model
{
    use MongodbDataTableTrait, HasFactory, SoftDeletes, CodeTrait;

    protected $collection = 'customers';
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
                'Description' => 'Customer Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Customer',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Customer Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Customer',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Customer Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Customer',
            ]);
        });
    }
}
