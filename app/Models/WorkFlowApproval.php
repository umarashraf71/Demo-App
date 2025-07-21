<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use App\Models\Log as Logs;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class WorkFlowApproval extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];


    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Work Flow Approval Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\WorkFlowApproval',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Work Flow Approval Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\WorkFlowApproval',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Work Flow Approval Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\WorkFlowApproval',
            ]);
        });
    }

    static $status = [
        0 => 'Pending',
        1 => 'Approved',
        2 => 'Rejected',
    ];

    public function user()
    {
        return $this->hasOne(User::class,  '_id', 'created_by')->select('name')->withTrashed();
    }

    public function updatedBy()
    {
        return $this->hasOne(User::class,  '_id', 'updated_by')->select('name')->withTrashed();
    }
    public function WorkFlow()
    {
        return $this->hasOne(Workflow::class,  '_id', 'workflow_id')->withTrashed();
    }

    public function prices()
    {
        return $this->hasMany(Price::class,  'code', 'code');
    }
}
