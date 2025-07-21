<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;

class Incentive extends Model
{
    use HasFactory;
    use MongodbDataTableTrait;
    use SoftDeletes;
    protected $collection = 'incentives';
    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];


    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Incentive Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Incentive',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Incentive Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Incentive',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Incentive Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Incentive',
            ]);
        });
    }
    public function incentive()
    {
        return $this->hasOne(IncentiveType::class,  '_id', 'incentive_type');
    }
    public function source()
    {
        return $this->hasOne(SupplierType::class,  '_id', 'source_type');
    }
}
