<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;

class MilkCollectionVehicle extends Model
{
    use HasFactory;
    use MongodbDataTableTrait;
    use SoftDeletes;
    protected $collection = 'milk_collection_vehicles';
    protected $dates = ['deleted_at'];
    protected $guarded = [
        'id'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Milk Collection Vehicle Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MilkCollectionVehicle',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Milk Collection Vehicle Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MilkCollectionVehicle',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Milk Collection Vehicle Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MilkCollectionVehicle',
            ]);
        });
    }

    public function vendor()
    {
        return $this->hasOne(VendorProfile::class,  '_id', 'company');
    }

    public function routeId()
    {
        return $this->hasMany(RouteVehicle::class, 'vehicle_id', '_id')->where('status', 1);
    }
}
