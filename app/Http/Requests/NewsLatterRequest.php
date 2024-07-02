<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Http\Exceptions\HttpResponseException;

class NewsLatterRequest extends FormRequest
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
                'email'=>'required|email|unique:news_letters,email',
            ];
    }

<<<<<<< HEAD
    public function failedValidation(ValidationValidator $validate){
        throw new HttpResponseException(response()->json([
            'success'=>false,
            'status'=>422,
            'message' => $validate->errors()->first(),
        ]));
=======
    public function failedValidation(ValidationValidator $validator){
        throw new HttpResponseException(
            response()->json([
                'code'=>401,
                'message' => 'Validation errors',
                'message' => $validator->errors()->first(),
            ],401),
        );
>>>>>>> b2580ed (#112: [Backend admin - api Integration] Language crud)
    }
}
