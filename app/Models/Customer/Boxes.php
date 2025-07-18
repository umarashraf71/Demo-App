<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boxes extends Model
{
    protected $connection = 'mongodbCustomer';
    
    use HasFactory;
}
