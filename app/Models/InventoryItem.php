<?php

namespace App\Models;

use App\Traits\CodeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Jenssegers\Mongodb\Eloquent\Model;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;
use Jenssegers\Mongodb\Eloquent\Builder;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use App\Models\AreaOffice;

class InventoryItem extends Model
{
    use MongodbDataTableTrait, HasFactory, SoftDeletes, CodeTrait;

    protected $collection = 'inventory_items';
    protected $dates = ['deleted_at'];
    protected $guarded = [
        'id'
    ];

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

        self::creating(function ($model) {
            $model->code = ($model::withTrashed()->count() + 1);
        });
        
        static::created(function ($item) {
            Logs::create([
                'Description' => 'Inventory Item Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\InventoryItem',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Inventory Item Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\InventoryItem',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Inventory Item Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\InventoryItem',
            ]);
        });
    }


    public function type()
    {
        return $this->hasOne(InventoryItemType::class,  '_id', 'item_type');
    }
    public function area_office()
    {
        return $this->belongsTo(AreaOffice::class,'area_office_id');
    }
}
