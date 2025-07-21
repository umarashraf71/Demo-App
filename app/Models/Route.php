<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;
use Jenssegers\Mongodb\Eloquent\Builder;

class Route extends Model
{
    use HasFactory;
    use MongodbDataTableTrait;
    use SoftDeletes;
    protected $collection = 'routes';
    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];


    protected static function booted(): void
    {
        $currentUrlPath = parse_url(url()->current(), PHP_URL_PATH);
        $apiPath = '/api/';

        if(Str::contains($currentUrlPath, $apiPath) == false)
        {
            $areaOfficeIds = AreaOffice::pluck('_id');

            static::addGlobalScope('accessLevel', function (Builder $builder) use ($areaOfficeIds) {
                $builder->whereIn('area_office_id', $areaOfficeIds);
            });
        }
        
        static::created(function ($item) {
            Logs::create([
                'Description' => 'Route Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Route',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Route Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Route',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Route Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Route',
            ]);
        });
    }

    public function RouteVehicle()
    {
        return $this->hasOne(RouteVehicle::class);
    }

}
