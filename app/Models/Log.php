<?php

namespace App\Models;

use App\Traits\ActiveStatusTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Pimlie\DataTables\Traits\MongodbDataTableTrait;
use Jenssegers\Mongodb\Relations\MorphTo;
use Log as Logs;

class Log extends Model
{
    use MongodbDataTableTrait, HasFactory, SoftDeletes, ActiveStatusTrait;
    protected $collection = 'logs';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'Description', 'user_id', 'logable_id', 'logable_type'
    ];

    public function logable()
    {
        return $this->morphTo();
    }

    public function getLogableWithTrashedAttribute()
    {
        $relation = $this->logable();
        $relation->withTrashed();

        return $relation->getResults();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
