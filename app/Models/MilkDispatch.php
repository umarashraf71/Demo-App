<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;
use App\Models\GatePassToken;

class MilkDispatch extends Model
{
    use HasFactory;
    use MongodbDataTableTrait;
    use SoftDeletes;
    protected $collection = 'milk_dispatches';
    protected $dates = ['deleted_at'];
    protected $guarded = [
        'id'
    ];
    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Milk Dispatch Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MilkDispatch',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Milk Dispatch Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MilkDispatch',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Milk Dispatch Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MilkDispatch',
            ]);
        });

        self::creating(function ($model) {
            $model->serial_number = ($model::withTrashed()->count() + 1);
        });
    }

    protected function setGrossVolumeAttribute($attr)
    {
        $this->attributes['gross_volume'] = (float)$attr;
    }
    protected function setVolumeTsAttribute($attr)
    {
        $this->attributes['volume_ts'] = (float)$attr;
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id', '_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', '_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id', '_id');
    }

    public function routeVehicle()
    {
        return $this->belongsTo(MilkCollectionVehicle::class, 'vehicle_id', '_id');
    }
    public function tokenNumber()
    {
        return $this->belongsTo(GatePassToken::class);
    }
    public function areaOffice()
    {
        return $this->belongsTo(AreaOffice::class);
    }

    public function mmt()
    {
        return $this->hasOne(User::class,  '_id', 'mmt_id');
    }
}
