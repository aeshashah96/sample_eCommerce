<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Http\Exceptions\HttpResponseException;


class BannerValidationRequest extends FormRequest
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
                'image' => 'required | mimes:jpeg,jpg,png,gif',
                'description'=>'required',
                'banner_title'=>'required',
                'sub_category_id'=>'required'
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
