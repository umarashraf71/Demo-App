<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Jenssegers\Mongodb\Eloquent\Builder;
use App\Models\Plant;

class MilkPurchase extends Model
{
    use HasFactory;
    use MongodbDataTableTrait;
    use SoftDeletes;
    protected $collection = 'milk_purchases';
    // protected $dates = ['deleted_at', 'created_at'];
    // protected $casts = ['created_at' => 'datetime'];
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
                    $query->whereIn('cp_id', $collectionPointIds)
                        ->orWhereIn('mcc_id', $collectionPointIds)
                        ->orWhereIn('area_office_id', $areaOfficeIds)
                        ->orWhereIn('plant_id', $plantIds);
                });
            });
        }


        static::created(function ($item) {
            Logs::create([
                'Description' => 'Milk Purchase Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MilkPurchase',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Milk Purchase Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MilkPurchase',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Milk Purchase Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MilkPurchase',
            ]);
        });

        self::creating(function ($model) {
            $model->serial_number = ($model::count() + 1);
            //          $model->serial_number = 'mpr_' . ($model::count() + 1);
        });
    }

    protected function setGrossVolumeAttribute($attr)
    {
        $this->attributes['gross_volume'] = (float)$attr;
    }
    protected function setTsVolumeAttribute($attr)
    {
        $this->attributes['ts_volume'] = (float)$attr;
    }
    protected function getTimeAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d M, y h:i A');
    }
    public function mcc()
    {
        return $this->hasOne(CollectionPoint::class,  '_id', 'mcc_id');
    }

    public function supplier()
    {
        return $this->hasOne(Supplier::class,  '_id', 'supplier_id')->withTrashed();
    }

    public function cp()
    { 
        return $this->hasOne(CollectionPoint::class,  '_id', 'cp_id');
    }
    public function user()
    {
        return $this->hasOne(User::class,  '_id', 'created_by');
    }

    public function ao()
    {
        return $this->belongsTo(AreaOffice::class, 'area_office_id');
    }

    public function plant()
    {
        return $this->belongsTo(plant::class, 'plant_id');
    }
}