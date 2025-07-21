<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
class PurchaseReciptRequest extends FormRequest
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
            'supplier_id' => 'required|max:50',
            'gross_volume'=> 'required',
            'mcc_id'=> 'required',
        ];
    }

    public function messages()
    {
        return [
            'supplier_id.required' => 'Supplier is required',
            'mcc_id.required' => 'Collection Point is required',

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
