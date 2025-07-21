<?php

namespace App\Models;

use App\Traits\ActiveStatusTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;

class SupplierType extends Model
{
    use HasFactory, MongodbDataTableTrait, SoftDeletes, ActiveStatusTrait;
    protected $collection = 'supplier_types';
    protected $dates = ['deleted_at'];
    protected $guarded = [
        'id'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Supplier Type Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\SupplierType',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Supplier Type Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\SupplierType',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Supplier Type Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\SupplierType',
            ]);
        });
    }
    // domain= 1:mcc, 2:area office, 3:plant

    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'supplier_type_id', '_id');
    }

    public function supliercategory()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }





















    public function price()
    {
        return $this->hasOne(Price::class,  'from_id', '_id')->where('type', 'supplier_type');
    }
}
