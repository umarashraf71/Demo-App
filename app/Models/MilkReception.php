<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;
use Jenssegers\Mongodb\Eloquent\Builder;
use App\Models\Plant;
use App\Models\User;

class MilkReception extends Model
{
    use HasFactory;
    use MongodbDataTableTrait;
    use SoftDeletes;
    protected $collection = 'milk_receptions';
    protected $dates = ['deleted_at'];
    protected $guarded = [
        'id'
    ];

    protected static function booted(): void
    {
        $currentUrlPath = parse_url(url()->current(), PHP_URL_PATH);
        $apiPath = '/api/';

        if(Str::contains($currentUrlPath, $apiPath) == false)
        {

            $collectionPointIds = CollectionPoint::pluck('_id');
            $areaOfficeIds = AreaOffice::pluck('_id');
            $plantIds = Plant::pluck('_id');

            static::addGlobalScope('accessLevel', function (Builder $builder) use ($collectionPointIds,$areaOfficeIds,$plantIds) {

                $builder->where(function ($query) use ($collectionPointIds, $areaOfficeIds, $plantIds) {
                    $query->whereIn('mcc_id', $collectionPointIds)
                        ->orWhereIn('area_office_id', $areaOfficeIds)
                        ->orWhereIn('plant_id', $plantIds);
                });
            });
        }

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Milk Reception Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MilkReception',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Milk Reception Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MilkReception',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Milk Reception Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MilkReception',
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
    protected function setLeftOverMilkAttribute($attr)
    {
        $this->attributes['left_over_milk'] = (float)$attr;
    }
    protected function gettoTimeAttribute($value)
    {
        if ($value === null) {
            return null;
        }
        try {
            $carbon = Carbon::createFromFormat('Y-m-d H:i:s', $value);
            return $carbon->format('d M, y h:i A');
        } catch (Exception $e) {
            return $value; // or any default value you want to display
        }
    }

    protected function geVolumeTsAttribute($value)
    {
        if ($value === null) {
            return null;
        }
        return number_format($value, 2);
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
        return $this->belongsTo(plant::class, 'plant_id');
    }
    public function user()
    {
        return $this->hasOne(User::class,  '_id', 'created_by');
    }
    public function RouteVehicle()
    {
        return $this->hasOne(RouteVehicle::class);
    }
    public function gateinfo()
    {
        return $this->hasOne(GatePassToken::class, '_id', 'gate_pass_token_id');
    }
}
