<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class PasswordReset extends Model
{
    protected $guarded = [
        'id'
    ];
    use HasFactory;
}
