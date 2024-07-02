<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AdminEditProfileRequest extends FormRequest
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_number'=>'required|numeric',
            'admin_logo' => 'mimes:jpeg,jpg,png,gif'
        ];
    }

    public function failedValidation(Validator $validate)
    {
        throw new HttpResponseException(
            response()->json([
                'code'=>401,
                'message' => 'Validation errors',
                'message' => $validate->errors()->first(),
            ],401),
        );
    }
}
