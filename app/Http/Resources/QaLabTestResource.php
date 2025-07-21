<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QaLabTestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request){
    //processing of numeric fields --- checks for sending text base response
        $this->test_type = ($this->test_type == 1) ? 'Quantitative' : 'Qualitative';

        if ($this->test_data_type == 1)
            $this->test_data_type = 'Range';

        else if ($this->test_data_type == 2)
            $this->test_data_type = 'Positive/Negative';
        else if ($this->test_data_type == 3)
            $this->test_data_type = 'Yes/No';

        if ($this->positive_negative !== null && $this->positive_negative == 0)
            $this->positive_negative = '-ve';
        else if ($this->positive_negative !== null && $this->positive_negative == 1)
            $this->positive_negative = '+ve';

        if ($this->yes_or_no !== null && $this->yes_or_no == 0)
            $this->yes_or_no = 'No';
        else if ($this->yes_or_no == 1)
            $this->yes_or_no = 'Yes';

        if ($this->rejection == 1)
            $this->rejection = '+ve';
        else if ($this->rejection == 2)
            $this->rejection = '-ve';
        else if ($this->rejection == 3)
            $this->rejection = 'Greater than Maximum Value';
        else if ($this->rejection == 4)
            $this->rejection = 'Less than Minimum Value';
        else if ($this->rejection == 5)
            $this->rejection = 'Out of Value Range';
        else if ($this->rejection == 6)
            $this->rejection = 'Yes';
        else if ($this->rejection == 7)
            $this->rejection = 'No';
        else if ($this->rejection == 8)
            $this->rejection = 'No Rejection';

        //return response
        return [
            'id' => $this->id,
            'qa_test_name' => $this->qa_test_name,
            'test_type' => $this->test_type,
            'test_data_type' => $this->test_data_type,
            'max' => $this->max,
            'min' => $this->min,
            'positive_negative' => $this->positive_negative,
            'yes_or_no' => $this->yes_or_no,
            'rejection' => $this->rejection,
            'exceptional_release' => $this->exceptional_release,
            'unit_of_measure' => $this->uom->name,
        ];
    }
}
