<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     *  Filters to be applied to the input.
     *
     *  @return array
     */
    public function filters()
    {
        return [
            'after' => [
                'email'            => 'cast:string',
                'password'         => 'cast:string',
            ],
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'            => 'required|string|email|unique:users',
            'password'         => 'required|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required'            => 'O campo EMAIL é obrigatório',
            'email.string'              => 'O campo EMAIL deve ser do tipo string',
            'email.email'               => 'O campo deve ser do tipo EMAIL',
            'password.required'         => 'O campo SENHA é obrigatório',
            'password.string'           => 'O campo SENHA deve ser do tipo string',
        ];
    }
}
