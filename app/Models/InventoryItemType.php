<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;

class InventoryItemType extends Model
{
    use MongodbDataTableTrait, HasFactory, SoftDeletes;

    protected $collection = 'inventory_item_types';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name', 'description', 'created_by', 'updated_by'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Inventory Item Type Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\InventoryItemType',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Inventory Item Type Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\InventoryItemType',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Inventory Item Type Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\InventoryItemType',
            ]);
        });
    }
}
