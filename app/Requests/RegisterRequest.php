<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string', // name maydoni uchun talablar qo'shildi
            'login' => 'required',
            'password' => 'required',
        ];
    }

    public function messages()
    {
        return [
//            'name.required' => 'The name field is required.',
//            'name.string' => 'The name field must be a string.',
            'email.required' => 'The email field is required.',
            'password.required' => 'The password field is required.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'error'   => 403,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }


}
