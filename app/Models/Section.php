<?php

namespace App\Models;

use App\Traits\ActiveStatusTrait;
use App\Traits\CodeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;
use Jenssegers\Mongodb\Eloquent\Builder;

class Section extends Model
{
    use MongodbDataTableTrait, HasFactory, SoftDeletes, CodeTrait, ActiveStatusTrait;

    protected $collection = 'sections';
    protected $dates = ['deleted_at'];
    protected $guarded = [
        'id'
    ];

    protected static function booted():void
    {

        $currentUrlPath = parse_url(url()->current(), PHP_URL_PATH);
        $apiPath = '/api/';

        if(Str::contains($currentUrlPath, $apiPath) == false)
        {

            if (auth()->user()->roles->first()->access_level == 5 && auth()->user()->roles->first()->name <> 'Super Admin') {
                static::addGlobalScope('accessLevel', function (Builder $builder) {
                    $builder->whereIn('dept_id', auth()->user()->access_level_ids);
                });
            }
            else if (auth()->user()->roles->first()->access_level == 4 && auth()->user()->roles->first()->name <> 'Super Admin') {
                static::addGlobalScope('accessLevel', function (Builder $builder) {
                    $builder->whereIn('_id', auth()->user()->access_level_ids);
                });
            }
            else if (auth()->user()->roles->first()->access_level == 3 && auth()->user()->roles->first()->name <> 'Super Admin') {
                
                $section_ids_array = []; 
                $zones = auth()->user()->zone;

                foreach($zones as $zone)
                {
                    if($zone && ($zone->section_id))
                    {
                        array_push($section_ids_array,$zone->section_id);
                    }
                }

                $section_ids_array = array_unique($section_ids_array);

                static::addGlobalScope('accessLevel', function (Builder $builder) use ($section_ids_array) {
                    $builder->whereIn('_id', $section_ids_array);
                });
            }
            else if (auth()->user()->roles->first()->access_level == 2 && auth()->user()->roles->first()->name <> 'Super Admin') {

                $section_ids_array = [];
                $areaOffices = auth()->user()->areaOffice;

                foreach($areaOffices as $areaOffice)
                {
                    if($areaOffice && ($areaOffice->zone))
                    {
                        $zone = $areaOffice->zone;

                        if($zone && ($zone->section_id))
                        {
                            array_push($section_ids_array, $zone->section_id);
                        }
                    }
                }

                $section_ids_array = array_unique($section_ids_array);

                static::addGlobalScope('accessLevel', function (Builder $builder) use ($section_ids_array) {
                    $builder->whereIn('_id', $section_ids_array);
                });
            }
            else if (auth()->user()->roles->first()->access_level == 1 && auth()->user()->roles->first()->name <> 'Super Admin') {

                $section_ids_array = [];
                $collectionCenters = auth()->user()->mcc;

                foreach($collectionCenters as $collectionCenter)
                {

                    if($collectionCenter && ($collectionCenter->area_office))
                    {
                        $areaOffice = $collectionCenter->area_office;
                        
                        if($areaOffice && ($areaOffice->zone))
                        {
                            $zone = $areaOffice->zone;

                            if($zone && ($zone->section_id))
                            {
                                array_push($section_ids_array, $zone->section_id);
                            }
                        }
                        
                    }
                }

                $section_ids_array = array_unique($section_ids_array);

                static::addGlobalScope('accessLevel', function (Builder $builder) use ($section_ids_array) {
                    $builder->whereIn('_id', $section_ids_array);
                });
            }
        }


        self::creating(function ($model) {
            $model->code = ($model::withTrashed()->count() + 1);
        });
        
        static::created(function ($item) {
            Logs::create([
                'Description' => 'Section Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Section',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Section Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Section',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Section Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Section',
            ]);
        });
    }
    
    public function department()
    {
        return $this->hasOne(Department::class,  '_id', 'dept_id');
    }
    public function userHasaccess()
    {
        return $this->belongsToMany(User::class, null, 'access_level_ids');
    }

    public function zones()
    {
        return $this->hasMany(Zone::class);
    }
}
