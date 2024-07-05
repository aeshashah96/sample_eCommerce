<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class SettingRequest extends FormRequest
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
                'LOGO' => 'mimes:jpeg,jpg,png',
                'INSTAGRAM' => 'url',
                'TWITTER' => 'url',
                'FACEBOOK' => 'url',
                'ADDRESS' => 'string',
                'MAIL' => 'email',
                'CONTACT' => 'numeric',
                'GMAP' => 'url',


        ];
    }

    public function messages()
    {
        return [
            'INSTAGRAM' => 'The instagram field must be a valid URL',
            'TWITTER' => 'The twitter field must be a valid URL',
            'FACEBOOK' => 'The facebook field must be a valid URL',
            'ADDRESS' => 'Enter valid address',
            'MAIL' => 'The mail field must be a valid email address',
            'CONTACT' => 'Enter valid contact',
            'GMAP' => 'The GMAP field must be a valid URL',
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
