<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use App\Traits\ActiveStatusTrait;
use App\Traits\CodeTrait;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;

class District extends Model
{
    use MongodbDataTableTrait, HasFactory, SoftDeletes, CodeTrait, ActiveStatusTrait;
    protected $collection = 'districts';
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
                'Description' => 'District Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\District',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'District Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\District',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'District Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\District',
            ]);
        });
    }
}
