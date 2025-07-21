<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;

class SupplierTestBaseIncentive extends Model
{
    use HasFactory;
    use MongodbDataTableTrait;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $guarded = [
        'id'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Supplier Test Base Incentive Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\SupplierTestBaseIncentive',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Supplier Test Base Incentive Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\SupplierTestBaseIncentive',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Supplier Test Base Incentive Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\SupplierTestBaseIncentive',
            ]);
        });
    }

    public function qa_test()
    {
        return $this->hasOne(QaLabTest::class,  '_id', 'qa_test_id');
    }
    public function supplier()
    {
        return $this->hasOne(Supplier::class,  '_id', 'supplier_id');
    }
    public function incentive()
    {
        return $this->hasOne(Incentive::class,  '_id', 'incentive_id');
    }
    public function type_incentive()
    {
        return $this->hasOne(IncentiveType::class,  '_id', 'incentive_type');
    }
}
