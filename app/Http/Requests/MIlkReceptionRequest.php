<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
class MIlkReceptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'mcc_id' => 'required',
            'gross_volume'=> 'required',
            'volume_ts'=> 'required',
            'left_over_milk'=> 'required',
        ];
    }

    public function messages()
    {
        return [
            'mcc_id.required' => 'Collection Center is required',
            'gross_volume.required' => 'Volume is required',
            'left_over_milk.required' => 'Volume 13% TS is required',

        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'isSuccessful'   => false,
            'responseMessage'=> $validator->errors()->first(),
            'errorMessage' => $validator->errors()
        ]));
    }
}
