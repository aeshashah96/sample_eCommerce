<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdminValidationRequest extends FormRequest
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
                'email' => 'required | email',
                'password' => 'required'
            ];
    }

    public function failedValidation(ValidationValidator $validate){
        throw new HttpResponseException(response()->json([
            'success'=>false,
            'message' => 'validation error',
            'data' => $validate->errors()
        ]));
    }
}
