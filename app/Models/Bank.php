<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;
use Illuminate\Support\Facades\Log;

class Bank extends Model
{
    use HasFactory;
    use MongodbDataTableTrait;
    use SoftDeletes;
    protected $collection = 'banks';
    protected $dates = ['deleted_at'];
    protected $guarded = [
        'id'
    ];

    public static function boot()
    {
        parent::boot();

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Bank Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Bank',
            ]);
        });

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Bank Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Bank',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Bank Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Bank',
            ]);
        });
    }

    public function handleAjaxUpdate(array $attributes)
    {
        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Bank Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Bank',
            ]);
        });

        return;
    }
}
