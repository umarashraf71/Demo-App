<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Jenssegers\Mongodb\Eloquent\Builder;
use App\Models\Log as Logs;
use Auth;

class RouteVehicle extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $collection = 'route_vehicles';
    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        $currentUrlPath = parse_url(url()->current(), PHP_URL_PATH);
        $apiPath = '/api/';

        if(Str::contains($currentUrlPath, $apiPath) == false)
        {
            $areaOfficeIds = AreaOffice::pluck('_id');
            $routeIds = Route::whereIn('area_office_id', $areaOfficeIds)->pluck('_id')->toArray();

            static::addGlobalScope('accessLevel', function (Builder $builder) use ($routeIds) {
                $builder->whereIn('route_id', $routeIds);
            });
        }

        self::creating(function ($model) {
            $model->reception = (int) 0;
        });

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Route Vehicle Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\RouteVehicle',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Route Vehicle Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\RouteVehicle',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Route Vehicle Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\RouteVehicle',
            ]);
        });
    }
    protected function setLatAttribute($attr)
    {
        $this->attributes['lat'] = (float)$attr;
    }
    protected function setLngAttribute($attr)
    {
        $this->attributes['lng'] = (float)$attr;
    }
    public function user()
    {
        return $this->hasOne(User::class,  '_id', 'user_id');
    }
    public function route()
    {
        return $this->hasOne(Route::class,  '_id', 'route_id');
    }
    public function vehicle()
    {
        return $this->hasOne(MilkCollectionVehicle::class,  '_id', 'vehicle_id');
    }

    public function milkReception()
    {
        return $this->hasOne(MilkReception::class,  '_id', 'milk_reception_id');
    }
}
