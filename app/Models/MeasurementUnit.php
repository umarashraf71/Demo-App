<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;

class MeasurementUnit extends Model
{
    use HasFactory;
    use MongodbDataTableTrait;
    use SoftDeletes;
    protected $collection = 'measurement_units';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name', 'description', 'created_by', 'updated_by'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Measurement Unit Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MeasurementUnit',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Measurement Unit Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MeasurementUnit',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Measurement Unit Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MeasurementUnit',
            ]);
        });
    }
}
