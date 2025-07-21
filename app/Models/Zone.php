<?php

namespace App\Models;

use App\Traits\ActiveStatusTrait;
use App\Traits\CodeTrait;
use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;
use Jenssegers\Mongodb\Eloquent\Builder;
use App\Models\Log as Logs;

class Zone extends Model
{
    use MongodbDataTableTrait, HasFactory, SoftDeletes, CodeTrait, ActiveStatusTrait;

    protected $collection = 'zones';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'code', 'section_id', 'name', 'address', 'contact', 'created_by', 'updated_by'
    ];

    protected static function booted(): void
    {
        $currentUrlPath = parse_url(url()->current(), PHP_URL_PATH);
        $apiPath = '/api/';

        if(Str::contains($currentUrlPath, $apiPath) == false)
        {
            if (auth()->user()->roles->first()->access_level == 5 && auth()->user()->roles->first()->name <> 'Super Admin') {
                
                $departments = auth()->user()->department;
                $sectionArray = [];

                foreach($departments as $department)
                {
                    $sections = $department->sections;

                    if($sections)
                    {
                        //iterating over each section and getting the zone's and storing them in array
                        foreach($sections as $section)
                        {
                            array_push($sectionArray,$section->Array);
                        }
                    }
                }

                $sectionIds = array_unique($sectionArray);

                static::addGlobalScope('accessLevel', function (Builder $builder) use ($sectionIds) {
                    $builder->whereIn('section_id',  $sectionIds);
                });
            }
            //access level filtering on the parent --- collection points parent is zone
            if (auth()->user()->roles->first()->access_level == 4 && auth()->user()->roles->first()->name <> 'Super Admin') {
                static::addGlobalScope('accessLevel', function (Builder $builder) {
                    $builder->whereIn('section_id', auth()->user()->access_level_ids);
                });
            }
            //access level on the same model --- only associated area office will be returned
            else if (auth()->user()->roles->first()->access_level == 3 && auth()->user()->roles->first()->name <> 'Super Admin') {
                static::addGlobalScope('accessLevel', function (Builder $builder) {
                    $builder->whereIn('_id', auth()->user()->access_level_ids);
                });
            }
            //access level on the same model --- no area offices will be returned
            else if (auth()->user()->roles->first()->access_level == 2 && auth()->user()->roles->first()->name <> 'Super Admin') {

                $zone_ids_array = [];
                $areaOffices = auth()->user()->areaOffice;

                foreach($areaOffices as $areaOffice)
                {
                    if($areaOffice && ($areaOffice->zone_id))
                    {
                        array_push($zone_ids_array, $areaOffice->zone_id);
                    }
                }

                $zone_ids_array = array_unique($zone_ids_array);

                static::addGlobalScope('accessLevel', function (Builder $builder) use ($zone_ids_array) {
                    $builder->whereIn('_id', $zone_ids_array);
                });
            }
            //access level on the same model --- no area offices will be returned
            else if (auth()->user()->roles->first()->access_level == 1 && auth()->user()->roles->first()->name <> 'Super Admin') {

                $zone_ids_array = [];
                $collectionCenters = auth()->user()->mcc;

                foreach($collectionCenters as $collectionCenter)
                {
                    if($collectionCenter && ($collectionCenter->area_office))
                    {
                        $areaOffice = $collectionCenter->area_office;

                        if($areaOffice && ($areaOffice->zone_id))
                        {
                            array_push($zone_ids_array, $areaOffice->zone_id);
                        }
                    }
                }

                $zone_ids_array = array_unique($zone_ids_array);

                static::addGlobalScope('accessLevel', function (Builder $builder) use ($zone_ids_array) {
                    $builder->whereIn('_id', $zone_ids_array);
                });
            }
        }
     
        self::creating(function ($model) {
            $model->code = ($model::withTrashed()->count() + 1);
        });
        
        static::created(function ($item) {
            Logs::create([
                'Description' => 'User Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\User',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'User Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\User',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'User Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\User',
            ]);
        });
    }

    public function section()
    {
        return $this->hasOne(Section::class,  '_id', 'section_id');
    }
    public function userHasaccess()
    {
        return $this->belongsToMany(User::class, null, 'access_level_ids');
    }

    public function areaOffice()
    {
        return $this->hasMany(AreaOffice::class);
    }
}
