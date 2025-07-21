<?php

namespace App\Models;

use App\Traits\ActiveStatusTrait;
use App\Traits\CodeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;

class Plant extends Model
{

    use MongodbDataTableTrait, HasFactory, SoftDeletes, CodeTrait, ActiveStatusTrait;
    protected $collection = 'plants';
    protected $dates = ['deleted_at'];
    protected $guarded = [
        'id'
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->code = ($model::withTrashed()->count() + 1);
        });
        
        static::created(function ($item) {
            Logs::create([
                'Description' => 'Plant Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Plant',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Plant Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Plant',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Plant Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Plant',
            ]);
        });
    }

    public function userHasaccess()
    {
        return $this->belongsToMany(User::class, null, 'access_level_ids');
    }
    protected function setBalanceAttribute($attr)
    {
        $this->attributes['balance'] = (float)$attr;
    }
}
