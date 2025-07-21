<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;

class PriceLog extends Model
{
    use HasFactory;
    use MongodbDataTableTrait;
    use SoftDeletes;
    protected $collection = 'price_logs';
    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Price Log Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\PriceLog',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Price Log Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\PriceLog',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Price Log Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\PriceLog',
            ]);
        });
    }

    public function getPrice()
    {
        return $this->hasOne(Price::class,  '_id', 'price_id');
    }
    public function workFlow()
    {
        return $this->hasOne(WorkFlowApproval::class,  'code', 'code')->select('request_type', 'code');
    }
}
