<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class PasswordValidationRequest extends FormRequest
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
            'current_password' => 'required',
                'new_password' => 'required|min:4|regex:/^\S*$/u',
                'confirm_password' => 'required|same:new_password',
        ];
    }

    public function messages()
    {
        return [
            'confirm_password.reuired' => 'confirm_password is required.',
            'confirm_password' => 'new password and confirm password does not match.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success'=>false,
                'status'=>422,
                'message' => $validator->errors()->first(),
            ]),
        );
    }
}
