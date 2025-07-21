<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;

class LactometerChart extends Model
{
    use HasFactory;
    protected $collection = 'lactometer_chart';

    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Lactometer Chart Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\LactometerChart',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Lactometer Chart Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\LactometerChart',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Lactometer Chart Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\LactometerChart',
            ]);
        });
    }
}
