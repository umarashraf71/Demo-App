<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Maklad\Permission\Traits\HasRoles;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use App\Models\CollectionPoint;
use Auth;
use Maklad\Permission\Models\Role;
use App\Models\Log as Logs;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_name',
        'role_id',
        'phone',
        'mobile_user_only',
        'status',
        'access_level_ids',
        'parent_id',
        'whatsapp',
        'tracking_no'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            Logs::create([
                'Description' => 'User Created',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\User',
            ]);
        });

        static::updated(function ($item) {
            Logs::create([
                'Description' => 'User Updated',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\User',
            ]);
        });

        static::deleted(function ($item) {
            Logs::create([
                'Description' => 'User Deleted',
                'user_id' => Auth::user()->id,
                'logable_id' => $item->id,
                'logable_type' => 'App\Models\User',
            ]);
        });
    }
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function mcc()
    {
        return $this->belongsToMany(CollectionPoint::class, 'user_ids');
    }
    public function areaOffice()
    {
        return $this->belongsToMany(AreaOffice::class, 'user_ids');
    }
    public function zone()
    {
        return $this->belongsToMany(Zone::class, 'user_ids');
    }
    public function section()
    {
        return $this->belongsToMany(Section::class, 'user_ids');
    }
    public function department()
    {
        return $this->belongsToMany(Department::class, 'user_ids');
    }
    public function plant()
    {
        return $this->belongsToMany(Plant::class, 'user_ids');
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    public function RouteVehicle()
    {
        return $this->hasOne(RouteVehicle::class);
    }


    public function scopeWithTrashedRecords($query)
    {
        return $query->withTrashed();
    }
}
