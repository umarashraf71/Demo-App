<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use App\Models\MilkDispatch;
use App\Models\MilkCollectionVehicle;
use App\Models\Log as Logs;
use Auth;

class GatePassToken extends Model
{
    use HasFactory;
    protected $guarded = [
        'id'
    ];

    public function milkDispatches()
    {
        return $this->hasMany(MilkDispatch::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(MilkCollectionVehicle::class, 'vehicle_id');
    }

    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Gate Pass Token Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\GatePassToken',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Gate Pass Token Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\GatePassToken',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Gate Pass Token Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\GatePassToken',
            ]);
        });
    }
}
