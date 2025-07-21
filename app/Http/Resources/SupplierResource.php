<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{

    public function toArray($request)
    {
        $this->supplier_type = ($this->supplier_type )?ucfirst($this->supplier_type->name) : $this->supplier_type_id;
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'father_name' => $this->father_name,
            'supplier_type' => $this->supplier_type,
            'supplier_type_id' => $this->supplier_type_id,
        ];
    }
}
