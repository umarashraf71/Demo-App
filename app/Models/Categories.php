<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;
use App\Models\SupplierType;

class Categories extends Model
{
    use HasFactory;
    use MongodbDataTableTrait;
    use SoftDeletes;

    protected $collection = 'categories';
    protected $dates = ['deleted_at'];

    protected $fillable = ['category_name', 'created_by', 'updated_by'];

    public function supliersOfCategory()
    {
        return $this->belongsTo(SupplierType::class, 'supplier_type_id');
    }

    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Categories Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Categories',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Categories Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Categories',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Categories Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Categories',
            ]);
        });
    }
}
