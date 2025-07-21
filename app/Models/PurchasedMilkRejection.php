<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use App\Models\Log as Logs;
use Auth;

class PurchasedMilkRejection extends Model
{
    use HasFactory;
    protected $collection = 'purchased_milk_rejections';
    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];
    // protected static function booted(): void
    // {
    //     //access level filtering on the parent --- collection points parent is zone
    //     if (auth()->user()->roles->first()->access_level == 3 && auth()->user()->roles->first()->name <> 'Super Admin') {
    //         static::addGlobalScope('accessLevel', function (Builder $builder) {
    //             $builder->whereIn('zone_id', auth()->user()->access_level_ids);
    //         });
    //     }
    //     //access level on the same model --- only associated area office will be returned
    //     if (auth()->user()->roles->first()->access_level == 2 && auth()->user()->roles->first()->name <> 'Super Admin') {
    //         static::addGlobalScope('accessLevel', function (Builder $builder) {
    //             $builder->whereIn('_id', auth()->user()->access_level_ids);
    //         });
    //     }
    // }

    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Purchased Milk Rejection Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\PurchasedMilkRejection',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Purchased Milk Rejection Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\PurchasedMilkRejection',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Purchased Milk Rejection Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\PurchasedMilkRejection',
            ]);
        });
    }

    public function mcc()
    {
        return $this->hasOne(CollectionPoint::class,  '_id', 'mcc_id');
    }

    public function mmt()
    {
        return $this->hasOne(User::class,  '_id', 'mmt_id');
    }

    public function ao()
    {
        return $this->belongsTo(AreaOffice::class, 'area_office_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(MilkCollectionVehicle::class, 'vehicle_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id');
    }
    
    public function user()
    {
        return $this->hasOne(User::class,  '_id', 'created_by');
    }
}
