<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;

class IncentiveType extends Model
{
    use HasFactory;
    use MongodbDataTableTrait;
    use SoftDeletes;
    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Incentive Type Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\IncentiveType',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Incentive Type Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\IncentiveType',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Incentive Type Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\IncentiveType',
            ]);
        });
    }

    public function qa_test()
    {
        return $this->hasOne(QaLabTest::class,  '_id', 'qa_test_id');
    }
}
