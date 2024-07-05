<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserAddressValidation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
            return [
                'user_id'=>'required',
                'address_line_1' =>'required',
                'address_line_2' =>'required',
                'city'=>'required|string',
                'state'=>'required|string',
                'country'=>'required|string',
                'zipcode'=>'required|numeric',
                'ship_to_different_address'=>'required|boolean'
            ];
    }

    public function failedValidation(ValidationValidator $validator){
        throw new HttpResponseException(
            response()->json([
                'success'=>false,
                'status'=>422,
                'message' => $validator->errors()->first(),
            ]),
        );
    }
}
