<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Support\Str;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;
use Jenssegers\Mongodb\Eloquent\Builder;

class Price extends Model
{
    use HasFactory;
    use MongodbDataTableTrait;
    use SoftDeletes;
    protected $collection = 'prices';
    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();


        $currentUrlPath = parse_url(url()->current(), PHP_URL_PATH);
        $apiPath = '/api/';

        if(Str::contains($currentUrlPath, $apiPath) == false)
        {

            if (auth()->user()->roles->first()->access_level == 6 && auth()->user()->roles->first()->name <> 'Super Admin') {
            
                $plantIds = Plant::pluck('_id');  
                $priceIds =  Price::whereIn('plant', $plantIds)->pluck('_id')->toArray();
                
                array_unique($priceIds);
                //dd($priceIds);
                static::addGlobalScope('accessLevel', function (Builder $builder) use ($priceIds) {
                    $builder->whereIn('_id', $priceIds);
                });
            }
            else if (auth()->user()->roles->first()->access_level == 5 && auth()->user()->roles->first()->name <> 'Super Admin') {
            
                $departmentIds = Department::pluck('_id');  
                $priceIds =  Price::whereIn('department', $departmentIds)->pluck('_id')->toArray();

                array_unique($priceIds);
                static::addGlobalScope('accessLevel', function (Builder $builder) use ($priceIds) {
                    $builder->whereIn('_id', $priceIds);
                });
            }
            else if (auth()->user()->roles->first()->access_level == 4 && auth()->user()->roles->first()->name <> 'Super Admin') {
            
                $sectionIds = Section::pluck('_id')->toArray();
                $zoneIds = Zone::whereIn('section_id',$sectionIds)->pluck('_id')->toArray();
                $areaOfficeIds = AreaOffice::whereIn('zone_id', $zoneIds)->pluck('_id')->toArray();

                $priceIds =  Price::whereIn('area_office', $areaOfficeIds)->pluck('_id')->toArray();

                array_unique($priceIds);
                static::addGlobalScope('accessLevel', function (Builder $builder) use ($priceIds) {
                    $builder->whereIn('_id', $priceIds);
                });
            }
            else if (auth()->user()->roles->first()->access_level == 3 && auth()->user()->roles->first()->name <> 'Super Admin') {
            
                $departmentIds = Department::pluck('_id');  
                $sectionIds = Section::whereIn('dept_id',$departmentIds)->pluck('_id')->toArray();
                $zoneIds = Zone::whereIn('section_id',$sectionIds)->pluck('_id')->toArray();
                $areaOfficeIds = AreaOffice::whereIn('zone_id', $zoneIds)->pluck('_id')->toArray();

                $priceIds =  Price::whereIn('area_office', $areaOfficeIds)->pluck('_id')->toArray();
            
                array_unique($priceIds);
                static::addGlobalScope('accessLevel', function (Builder $builder) use ($priceIds) {
                    $builder->whereIn('_id', $priceIds);
                });
            }
            else if (auth()->user()->roles->first()->access_level == 2 && auth()->user()->roles->first()->name <> 'Super Admin') {
            
                $areaOfficeIds = AreaOffice::pluck('_id');
                $priceIds =  Price::whereIn('area_office', $areaOfficeIds)->pluck('_id')->toArray();
            
                array_unique($priceIds);
                static::addGlobalScope('accessLevel', function (Builder $builder) use ($priceIds) {
                    $builder->whereIn('_id', $priceIds);
                });
            }
            else if (auth()->user()->roles->first()->access_level == 1 && auth()->user()->roles->first()->name <> 'Super Admin') {
            
                $collectionPointIds = CollectionPoint::pluck('_id');
                $priceIds =  Price::whereIn('collection_point', $collectionPointIds)->pluck('_id')->toArray();
            
                array_unique($priceIds);
                static::addGlobalScope('accessLevel', function (Builder $builder) use ($priceIds) {
                    $builder->whereIn('_id', $priceIds);
                });
            }
            
        }

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Price Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Price',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Price Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Price',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Price Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Price',
            ]);
        });
    }
    //    Status:
    //            0:Created
    //            1:Approved
    //            2:Pending for Approval
    //            3: Rejected


    //            4: Reverted

    //    public function cp()
    //    {
    //        return $this->hasOne(CollectionPoint::class,  '_id','from_id');
    //    }


    public function source()
    {
        return $this->hasOne(SupplierType::class,  '_id', 'source_type')->select('id', 'name');
    }
    public function suplier()
    {
        return $this->hasOne(Supplier::class,  '_id', 'supplier')->select('id', 'name');
    }
    public function collPoint()
    {
        return $this->hasOne(CollectionPoint::class,  '_id', 'collection_point')->select('id', 'name');
    }
    public function areaOffice()
    {
        return $this->hasOne(AreaOffice::class,  '_id', 'area_office')->select('id', 'name');
    }


    //    public function supplier()
    //    {
    //        return $this->hasOne(Supplier::class,  '_id','supplier_id');
    //    }
    //    public function source_type()
    //    {
    //        return $this->hasOne(SupplierType::class,  '_id','st_id');
    //    }
}
