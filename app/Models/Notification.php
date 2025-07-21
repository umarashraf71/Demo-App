<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;

class Notification extends Model
{
    use HasFactory;
    use MongodbDataTableTrait;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'notifications';
    protected $guarded = [
        'id'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Notification Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Notification',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Notification Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Notification',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Notification Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Notification',
            ]);
        });
    }

    static $type = [
        1 => 'Price Approved',
        2 => 'Price Rejected',
        4 => 'Price Reverted',
        5 => 'Transfer Approved',
        6 => 'Transfer Rejected',
    ];
}
