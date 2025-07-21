<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;
use App\Traits\CodeTrait;


class MilkTransfer extends Model
{
    use HasFactory;
    use MongodbDataTableTrait;
    use SoftDeletes;
    use CodeTrait;
    protected $dates = ['deleted_at'];
    protected $collection = 'milk_transfers';

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
                'Description' => 'Milk Transfer Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MilkTransfer',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Milk Transfer Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MilkTransfer',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Milk Transfer Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MilkTransfer',
            ]);
        });

        self::creating(function ($model) {
            $number = "MT-" . ($model::count() + 1);
            $model->serial_number = $number;
            //          $model->serial_number = 'mpr_' . ($model::count() + 1);
        });
    }

    //    Status= 1:transferred 2:request approved 3:request rejected 0:pending

    public function fromCp()
    {
        return $this->hasOne(CollectionPoint::class,  '_id', 'from')->select('name');
    }
    public function toCp()
    {
        return $this->hasOne(CollectionPoint::class,  '_id', 'to')->select('name');
    }

    public function wfa()
    {
        return $this->hasOne(WorkFlowApproval::class, 'code', 'wf_code');
    }
    public function fromAo()
    {
        return $this->hasOne(AreaOffice::class,  '_id', 'from')->select('name');
    }
    public function toAo()
    {
        return $this->hasOne(AreaOffice::class,  '_id', 'to')->select('name');
    }

    public function user()
    {
        return $this->hasOne(User::class,  '_id', 'created_by')->select('name');
    }
    protected function setVolumeAttribute($attr)
    {
        $this->attributes['volume'] = (float)$attr;
    }
    protected function setVolumeReceivedAttribute($attr)
    {
        $this->attributes['volume_received'] = (float)$attr;
    }
}
