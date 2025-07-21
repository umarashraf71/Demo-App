<?php

namespace App\Models;

use App\Traits\ActiveStatusTrait;
use App\Traits\CodeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;
use Jenssegers\Mongodb\Eloquent\Builder;
use Illuminate\Support\Str;

class AreaOffice extends Model
{
    use MongodbDataTableTrait, HasFactory, SoftDeletes, CodeTrait, ActiveStatusTrait;
    protected $collection = 'area_offices';
    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];


    protected static function booted(): void
    {
        self::creating(function ($model) {
            $model->code = ($model::withTrashed()->count() + 1);
        });
        
        static::created(function ($item) {
            Logs::create([
                'Description' => 'Area Office Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\AreaOffice',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Area Office Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\AreaOffice',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Area Office Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\AreaOffice',
            ]);
        });

        $currentUrlPath = parse_url(url()->current(), PHP_URL_PATH);
        $apiPath = '/api/';

        if(Str::contains($currentUrlPath, $apiPath) == false)
        {

            //access level filtering on the parent --- area office and area office parent is zone and zone parent is section and section parent is department 
            if (auth()->user()->roles->first()->access_level == 5 && auth()->user()->roles->first()->name <> 'Super Admin') {
        
                static::addGlobalScope('accessLevel', function (Builder $builder) {
                
                    $departments = auth()->user()->department;
                    $zoneArray = [];

                    foreach($departments as $department)
                    {
                        $sections = $department->sections;

                        if($sections)
                        {
                                //iterating over each section and getting the zone's and storing them in array
                            foreach($sections as $section)
                            {
                                $zones = $section->zones;

                                foreach($zones as $zone)
                                {
                                    $idsArray = $zone->pluck('_id')->toArray();
                                    foreach($idsArray as $id)
                                    {
                                        array_push($zoneArray,$id);
                                    }
                                }
                            }
                        }
                    }

                    $zoneIds = array_unique($zoneArray);

                    $builder->whereIn('zone_id', $zoneIds);
                });
            }

            //access level filtering on the parent --- area office parent is zone and zone parent is section
            else if (auth()->user()->roles->first()->access_level == 4 && auth()->user()->roles->first()->name <> 'Super Admin') {

                //dd(auth()->user()->section);

                static::addGlobalScope('accessLevel', function (Builder $builder) {

                    $sections = auth()->user()->section;
                    $zoneArray = [];

                    //iterating over each section and getting the zone's and storing them in array
                    foreach($sections as $section)
                    {
                        $zones = $section->zones;

                        foreach($zones as $zone)
                        {
                            $idsArray = $zone->pluck('_id')->toArray();
                            foreach($idsArray as $id)
                            {
                                array_push($zoneArray,$id);
                            }
                        }
                    }

                    $zoneIds = array_unique($zoneArray);

                    $builder->whereIn('zone_id', $zoneIds);
                });
            }

            //access level filtering on the parent --- area office parent is zone
            else if (auth()->user()->roles->first()->access_level == 3 && auth()->user()->roles->first()->name <> 'Super Admin') {
                static::addGlobalScope('accessLevel', function (Builder $builder) {
                    $builder->whereIn('zone_id', auth()->user()->access_level_ids);
                });
            }
            //access level on the same model --- only associated area office will be returned
            else if (auth()->user()->roles->first()->access_level == 2 && auth()->user()->roles->first()->name <> 'Super Admin') {
                static::addGlobalScope('accessLevel', function (Builder $builder) {
                    $builder->whereIn('_id', auth()->user()->access_level_ids);
                });
            }
            //access level on the same model --- no area offices will be returned
            else if (auth()->user()->roles->first()->access_level == 1 && auth()->user()->roles->first()->name <> 'Super Admin') {

                $ao_ids_array = [];
                $collectionCenters = auth()->user()->mcc;

                foreach($collectionCenters as $collectionCenter)
                {
                    if($collectionCenter && ($collectionCenter->area_office_id))
                    {
                        array_push($ao_ids_array, $collectionCenter->area_office_id);
                    }
                }

                $ao_ids_array = array_unique($ao_ids_array);

                static::addGlobalScope('accessLevel', function (Builder $builder) use ($ao_ids_array) {
                    $builder->whereIn('_id', $ao_ids_array);
                });
            }

        }
    }

    protected function setBalanceAttribute($attr)
    {
        $this->attributes['balance'] = (float)$attr;
    }

    public function zone()
    {
        return $this->hasOne(Zone::class,  '_id', 'zone_id');
    }
    public function userHasaccess()
    {
        return $this->belongsToMany(User::class, null, 'access_level_ids');
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function tehsil()
    {
        return $this->belongsTo(Tehsil::class);
    }
}
