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
<<<<<<< HEAD
                'success' => false,
                'status'=>422,
                'message' => $validator->errors()->first(),
            ]),
=======
                'code'=>401,
                'message' => 'Validation errors',
                'message' => $validate->errors()->first(),
            ],401),
>>>>>>> b2580ed (#112: [Backend admin - api Integration] Language crud)
        );
    }
}
