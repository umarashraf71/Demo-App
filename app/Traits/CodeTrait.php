<?php

namespace App\Traits;

trait CodeTrait
{
    protected function getCodeAttribute($attr)
    {
        $called_class = get_called_class();
        $prepended_text = '';
        if ($called_class == 'App\Models\Plant') {
            $prepended_text = 'P';
        } elseif ($called_class == 'App\Models\Department') {
            $prepended_text = 'D';
        } elseif ($called_class == 'App\Models\Section') {
            $prepended_text = 'S';
        } elseif ($called_class == 'App\Models\Zone') {
            $prepended_text = 'Z';
        } elseif ($called_class == 'App\Models\AreaOffice') {
            $prepended_text = 'AO';
        } elseif ($called_class == 'App\Models\CollectionPoint') {
            $prepended_text = 'CP';
        } elseif ($called_class == 'App\Models\Supplier') {
            $prepended_text = 'SP';
        } elseif ($called_class == 'App\Models\VendorProfile') {
            $prepended_text = 'VP';
        } elseif ($called_class == 'App\Models\Customer') {
            $prepended_text = 'C';
        }
        $icon = ($prepended_text) ? '-' : '';
        $short_name = isset($this->area_office->short_name) ? '-' . $this->area_office->short_name : '';
        return $prepended_text . $icon . sprintf("%06d", $attr) . $short_name;
    }
}
