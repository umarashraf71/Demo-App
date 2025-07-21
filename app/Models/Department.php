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

class Department extends Model
{
    use MongodbDataTableTrait, HasFactory, SoftDeletes, CodeTrait, ActiveStatusTrait;
    protected $collection = 'departments';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'code', 'plant_id', 'name', 'address', 'latitude', 'longitude', 'created_by', 'updated_by'
    ];

    protected static function booted():void
    { 
        $currentUrlPath = parse_url(url()->current(), PHP_URL_PATH);
        $apiPath = '/api/';

        if(Str::contains($currentUrlPath, $apiPath) == false)
        {
        
            if (auth()->user()->roles->first()->access_level == 6 && auth()->user()->roles->first()->name <> 'Super Admin') {
                static::addGlobalScope('accessLevel', function (Builder $builder) {
                    $builder->whereIn('plant_id', auth()->user()->access_level_ids);
                });
            }
            else if (auth()->user()->roles->first()->access_level == 5 && auth()->user()->roles->first()->name <> 'Super Admin') {
                static::addGlobalScope('accessLevel', function (Builder $builder) {
                    $builder->whereIn('_id', auth()->user()->access_level_ids);
                });
            }
            else if (auth()->user()->roles->first()->access_level == 4 && auth()->user()->roles->first()->name <> 'Super Admin') {
                
                $department_ids_array = []; 
                $sections = auth()->user()->section;

                foreach($sections as $section)
                {    
                    if($section && ($section->dept_id))
                    {
                        array_push($department_ids_array,$section->dept_id);
                    }
                }

                $department_ids_array = array_unique($department_ids_array);

                static::addGlobalScope('accessLevel', function (Builder $builder) use ($department_ids_array) {
                    $builder->whereIn('_id', $department_ids_array);
                });
            }
            else if (auth()->user()->roles->first()->access_level == 3 && auth()->user()->roles->first()->name <> 'Super Admin') {
                
                $department_ids_array = []; 
                $zones = auth()->user()->zone;

                foreach($zones as $zone)
                {
                    if($zone && ($zone->section))
                    {
                        $section = $zone->section;

                        if($section && ($section->dept_id))
                        {
                            array_push($department_ids_array,$section->dept_id);
                        }
                    
                    }
                }

                $department_ids_array = array_unique($department_ids_array);

                static::addGlobalScope('accessLevel', function (Builder $builder) use ($department_ids_array) {
                    $builder->whereIn('_id', $department_ids_array);
                });
            }
            else if (auth()->user()->roles->first()->access_level == 2 && auth()->user()->roles->first()->name <> 'Super Admin') {
                
                $department_ids_array = []; 
                $areaOffices = auth()->user()->areaOffice;

                foreach($areaOffices as $areaOffice)
                {
                    if($areaOffice && ($areaOffice->zone))
                    {
                        $zone = $areaOffice->zone;

                        if($zone && ($zone->section))
                        {
                            $section = $zone->section;
                            
                            if($section && ($section->dept_id))
                            {
                                array_push($department_ids_array,$section->dept_id);
                            }
                        
                        }
                    }
                }

                $department_ids_array = array_unique($department_ids_array);

                static::addGlobalScope('accessLevel', function (Builder $builder) use ($department_ids_array) {
                    $builder->whereIn('_id', $department_ids_array);
                });
            }
            else if (auth()->user()->roles->first()->access_level == 1 && auth()->user()->roles->first()->name <> 'Super Admin') {
                
                $department_ids_array = []; 
                $collectionCenters = auth()->user()->mcc;

                foreach($collectionCenters as $collectionCenter)
                {
                    if($collectionCenter && ($collectionCenter->area_office))
                    {
                        $areaOffice = $collectionCenter->area_office;
                        
                        if($areaOffice && ($areaOffice->zone))
                        {
                            $zone = $areaOffice->zone;
                            
                            if($zone && ($zone->section))
                            {
                                $section = $zone->section;
                                
                                if($section && ($section->dept_id))
                                {
                                    array_push($department_ids_array,$section->dept_id);
                                }
                            
                            }
                        }
                    }
                }

                $department_ids_array = array_unique($department_ids_array);

                static::addGlobalScope('accessLevel', function (Builder $builder) use ($department_ids_array) {
                    $builder->whereIn('_id', $department_ids_array);
                });
            }
        }

        self::creating(function ($model) {
            $model->code = ($model::withTrashed()->count() + 1);
        });

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Department Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Department',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Department Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Department',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Department Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Department',
            ]);
        });
    }

    public function plant()
    {
        return $this->hasOne(Plant::class,  '_id', 'plant_id');
    }
    public function userHasaccess()
    {
        return $this->belongsToMany(User::class, null, 'access_level_ids');
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'dept_id');
    }
   
}
