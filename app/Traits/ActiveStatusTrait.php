<?php
namespace App\Traits;
trait ActiveStatusTrait
{
    public function scopeActive($query)
    {
        return $query->where('status',1);
    }
}

?>
