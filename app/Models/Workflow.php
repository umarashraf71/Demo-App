<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use App\Models\Log as Logs;
use Auth;

class Workflow extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    static $types = [
        1 => ['name' => 'Milk Base Price', 'sub_name' => 'Milk Base Price', 'status' => '1'],
        2 => ['name' => 'Milk Transfer (mcc to mcc)', 'sub_name' => 'Milk Transfer', 'status' => '1'],
        3 => ['name' => 'Milk Transfer (ao to ao)', 'sub_name' => 'Milk Transfer', 'status' => '1'],

    ];


    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            Logs::create([
                'Description' => 'Work Flow Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Workflow',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'Work Flow Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Workflow',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'Work Flow Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\Workflow',
            ]);
        });
    }

    public function roles()
    {
        return $this->belongsToMany(\Maklad\Permission\Models\Role::class, 'role_ids');
    }

    public function setDocumentTypeAttribute($value)
    {
        $this->attributes['document_type'] = (int)$value;
    }
}
