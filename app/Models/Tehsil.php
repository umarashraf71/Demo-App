<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use App\Traits\ActiveStatusTrait;
use App\Traits\CodeTrait;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;

class Tehsil extends Model
{
    use MongodbDataTableTrait, HasFactory, SoftDeletes, CodeTrait, ActiveStatusTrait;
    protected $collection = 'tehsils';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name', 'short_name', 'district_id'
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->code = ($model::withTrashed()->count() + 1);
        });
        
        static::created(function ($item) {
            Logs::create([
                'Description' => 'Tehsil Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Tehsil',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Tehsil Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Tehsil',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Tehsil Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Tehsil',
            ]);
        });
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
