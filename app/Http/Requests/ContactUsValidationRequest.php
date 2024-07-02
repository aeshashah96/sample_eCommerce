<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ContactUsValidationRequest extends FormRequest
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
            'name'=>'required',
            'email'=>'required|email',
            'subject'=>'required',
            'message'=>'required|min:5'
        ];
    }

    public function failedValidation(Validator $validate)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'status'=>422,
                'message' => $validate->errors()->first(),
            ]),
        );
    }
}
