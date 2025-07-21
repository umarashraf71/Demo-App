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
use App\Models\MilkPurchase;

class Supplier extends Model
{
    use MongodbDataTableTrait, HasFactory, SoftDeletes, CodeTrait, ActiveStatusTrait;

    protected $table = 'suppliers';
    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        self::creating(function ($model) {
            $model->code = ($model::withoutGlobalScope('accessLevel')->withTrashed()->count() + 1);
        });

        $currentUrlPath = parse_url(url()->current(), PHP_URL_PATH);
        $apiPath = '/api/';

        if(Str::contains($currentUrlPath, $apiPath) == false)
        {

            if (auth()->user()->roles->first()->name <> 'Super Admin') {
            $collectionPointIds = CollectionPoint::pluck('_id');
            $areaOfficeIds = AreaOffice::pluck('_id');
            $plantIds = Plant::pluck('_id');

            
            $supplierIds =  Supplier::whereIn('cp_ids', $collectionPointIds)
                            ->orWhereIn('mcc', $collectionPointIds)
                            ->orWhereIn('area_office', $areaOfficeIds)
                            ->orWhereIn('plant', $plantIds)->pluck('_id')->toArray();

            array_unique($supplierIds);


            static::addGlobalScope('accessLevel', function (Builder $builder) use ($supplierIds) {

                $builder->whereIn('_id', $supplierIds);

            });
        }
        }

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Supplier Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Supplier',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Supplier Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Supplier',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Supplier Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Supplier',
            ]);
        });
    }
    
    public function price()
    {
        return $this->hasOne(Price::class,  'from_id', '_id')->where('type', 'supplier');
    }

    public function supplier_type()
    {
        return $this->hasOne(SupplierType::class,  '_id', 'supplier_type_id');
    }

    //    public function cp_price()
    //    {
    //        return $this->hasOne(Price::class,  'supplier_id','_id')->where('type','supplier_cp');
    //    }

    //    public function collectionPoints()
    //    {
    //        return $this->hasMany(CollectionPoint::class,  'supplier','_id');
    //    }

    protected function setNtnAttribute($attr)
    {
        $this->attributes['ntn'] = str_replace('-', '', str_replace('_', '', $attr));
    }
    protected function setCnicAttribute($attr)
    {
        $this->attributes['cnic'] = str_replace('-', '', str_replace('_', '', $attr));
    }
    protected function setContactAttribute($attr)
    {
        $this->attributes['contact'] = str_replace('-', '', str_replace('_', '', $attr));
    }
    protected function setNextOfKinContactAttribute($attr)
    {
        $this->attributes['next_of_kin_contact'] = str_replace('-', '', str_replace('_', '', $attr));
    }
    protected function setWhatsappAttribute($attr)
    {
        $this->attributes['whatsapp'] = str_replace('-', '', str_replace('_', '', $attr));
    }
    public function collectionPoints()
    {
        return $this->belongsToMany(CollectionPoint::class, null, 'supplier_ids');
    }
    public function purchases()
    {
        return $this->hasMany(MilkPurchase::class,'supplier_id','_id');
    }
}
