<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;

class MilkRejection extends Model
{
    use HasFactory;
    use MongodbDataTableTrait;
    use SoftDeletes;
    public $incrementing = true;
    protected $collection = 'milk_rejections';
    protected $dates = ['deleted_at'];
    protected $guarded = [
        'id'
    ];
    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Milk Rejection Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MilkRejection',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Milk Rejection Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MilkRejection',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Milk Rejection Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\MilkRejection',
            ]);
        });

        self::creating(function ($model) {
            $model->serial_number = ($model::count() + 1);
            //          $model->serial_number = 'mpr_' . ($model::count() + 1);
        });
    }

    protected function setGrossVolumeAttribute($attr)
    {
        $this->attributes['gross_volume'] = (float)$attr;
    }
    protected function setTsVolumeAttribute($attr)
    {
        $this->attributes['ts_volume'] = (float)$attr;
    }

    public function mcc()
    {
        return $this->hasOne(CollectionPoint::class,  '_id', 'mcc_id');
    }

    public function supplier()
    {
        return $this->hasOne(Supplier::class,  '_id','supplier_id')->withTrashed();
    }

    public function cp()
    { 
        return $this->hasOne(CollectionPoint::class,  '_id', 'cp_id');
    }
    public function user()
    {
        return $this->hasOne(User::class,  '_id', 'created_by');
    }

    public function ao()
    {
        return $this->belongsTo(AreaOffice::class, 'area_office_id');
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class, 'plant_id');
    }
}
