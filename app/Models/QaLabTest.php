<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;
use App\Models\MeasurementUnit;

class QaLabTest extends Model
{
    use HasFactory;
    use MongodbDataTableTrait;
    use SoftDeletes;
    protected $collection = 'qa_lab_tests';
    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];
    public function uom()
    {
        return $this->belongsTo(MeasurementUnit::class, 'measurementunit_id');
    }

    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Qa Lab Test Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\QaLabTest',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Qa Lab Test Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\QaLabTest',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Qa Lab Test Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\QaLabTest',
            ]);
        });

        self::saving(function ($model) {
            $model->test_type = (int) $model->test_type;
            $model->test_data_type = (int) $model->test_data_type;
            $model->rejection = (int) $model->rejection;
            if ($model->min <> null) {
                $model->min = (float) $model->min;
                $model->max = (float) $model->max;
            }
            $model->exceptional_release = ($model->exceptional_release) ? (int) $model->exceptional_release : (int) 0;
            $model->is_test_based = ($model->is_test_based) ? (int) $model->is_test_based : (int) 0;
            if ($model->positive_negative <> null)
                $model->positive_negative = (int) $model->positive_negative;
            if ($model->yes_or_no <> null)
                $model->yes_or_no = (int) $model->yes_or_no;

            //type cast apply test array
            $apply_tests = $model->apply_test;
            foreach ($apply_tests as $key => $value) {
                $apply_tests[$key] = (int) $value;
            }
            $model->apply_test = $apply_tests;
        });
    }
}
