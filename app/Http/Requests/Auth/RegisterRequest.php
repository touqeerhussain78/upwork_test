<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        $response = [
            'message' => __('Error while regisering user.'),
            'errors' => $errors,
        ];

        throw new HttpResponseException(
            response()->json($response, JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
           'user_name'    => ['required', 'string', 'unique:users', 'min:4', 'max:20'],
           'email' => ['required', 'string', 'unique:users'],
           'password'     => ['required','string', 'min:6'],
           // 'password_confirmation' =>   ['required','same:password']
        ];
    }
}
