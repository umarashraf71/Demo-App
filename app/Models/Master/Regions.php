<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regions extends Model
{
    protected $connection = 'mongodbMaster';
    protected $collection = 'regions';

    use HasFactory;
}
