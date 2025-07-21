<?php

namespace App\Models;

use App\Traits\ActiveStatusTrait;
use App\Traits\CodeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;
use Jenssegers\Mongodb\Eloquent\Builder;
use Illuminate\Support\Str;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;

class CollectionPoint extends Model
{
    use MongodbDataTableTrait, HasFactory, SoftDeletes, CodeTrait, ActiveStatusTrait;

    protected $collection = 'collection_points';
    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];


    protected static function booted(): void
    {

        $currentUrlPath = parse_url(url()->current(), PHP_URL_PATH);
        $apiPath = '/api/';

        if(Str::contains($currentUrlPath, $apiPath) == false)
        {

            //access level filtering on the parent --- collection points parent is area office and area office parent is zone and zone parent is section and section parent is department 
            if (auth()->user()->roles->first()->access_level == 5 && auth()->user()->roles->first()->name <> 'Super Admin') {
                
                static::addGlobalScope('accessLevel', function (Builder $builder) {
                
                    $departments = auth()->user()->department;
                    $areaOfficeArray = [];

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
                                    $idsArray = $zone->areaOffice->pluck('_id')->toArray();
                                    foreach($idsArray as $id)
                                    {
                                        array_push($areaOfficeArray,$id);
                                    }
                                }
                            }
                        }
                    }

                    $areaOfficeIds = array_unique($areaOfficeArray);

                    $builder->whereIn('area_office_id', $areaOfficeIds);
                });
            }

            //access level filtering on the parent --- collection points parent is area office and area office parent is zone and zone parent is section
            else if (auth()->user()->roles->first()->access_level == 4 && auth()->user()->roles->first()->name <> 'Super Admin') {

                //dd(auth()->user()->section);

                static::addGlobalScope('accessLevel', function (Builder $builder) {

                    $sections = auth()->user()->section;
                    $areaOfficeArray = [];
        
                    //iterating over each section and getting the zone's and storing them in array
                    foreach($sections as $section)
                    {
                        $zones = $section->zones;
        
                        foreach($zones as $zone)
                        {
                            $idsArray = $zone->areaOffice->pluck('_id')->toArray();
                            foreach($idsArray as $id)
                            {
                                array_push($areaOfficeArray,$id);
                            }
                        }
                    }
        
                    $areaOfficeIds = array_unique($areaOfficeArray);

                    $builder->whereIn('area_office_id', $areaOfficeIds);
                });
            }

            //access level filtering on the parent --- collection points parent is area office and area office parent is zone
            else if (auth()->user()->roles->first()->access_level == 3 && auth()->user()->roles->first()->name <> 'Super Admin') 
            {
                // $zone = Zone::find(auth()->user()->access_level_ids)[0];
                // dd(auth()->user()->zone[0]->areaOffice->pluck('_id'));

                static::addGlobalScope('accessLevel', function (Builder $builder) {

                    $zones = auth()->user()->zone;
                    $areaOfficeArray = [];
        
                    foreach($zones as $zone)
                    {
                        $idsArray = $zone->areaOffice->pluck('_id')->toArray();
                        foreach($idsArray as $id)
                        {
                            array_push($areaOfficeArray,$id);
                        }
                    }

                    $areaOfficeIds = array_unique($areaOfficeArray);

                    $builder->whereIn('area_office_id', $areaOfficeIds);
                });
            }
            //access level filtering on the parent --- collection points parent is area office
            else if (auth()->user()->roles->first()->access_level == 2 && auth()->user()->roles->first()->name <> 'Super Admin') {

                static::addGlobalScope('accessLevel', function (Builder $builder) {
                    $builder->whereIn('area_office_id', auth()->user()->access_level_ids);
                });
            }
            //access level on the same model --- only associated collection point will be returned
            else if (auth()->user()->roles->first()->access_level == 1 && auth()->user()->roles->first()->name <> 'Super Admin') {
                static::addGlobalScope('accessLevel', function (Builder $builder) {
                    $builder->whereIn('_id', auth()->user()->access_level_ids);
                });
            }
        }


        
        self::creating(function ($model) {
            $model->code = ($model::withTrashed()->count() + 1);
        });

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Collection Point Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\CollectionPoint',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Collection Point Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\CollectionPoint',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Collection Point Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\CollectionPoint',
            ]);
        });
    }

    protected function setBalanceAttribute($attr)
    {
        $this->attributes['balance'] = (float)$attr;
    }
    public function area_office()
    {
        return $this->hasOne(AreaOffice::class,  '_id', 'area_office_id');
    }

    public function price()
    {
        return $this->hasOne(Price::class,  'from_id', '_id')->where('type', 'cp');
    }
    public function userHasaccess()
    {
        return $this->belongsToMany(User::class, null, 'access_level_ids');
    }

    public function collectionPointCategory()
    {
        return $this->belongsTo(Categories::class, 'category_id', '_id');
    }
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, null, 'cp_ids');
    }
}
