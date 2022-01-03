<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
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
                'password'         => 'cast:string',
                'confirm_password' => 'cast:string',
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
            'password'         => 'required|string|regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[a-z]).{8,20}$/',
            'confirm_password' => 'required|string|same:password',
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
            'password.required'         => 'O campo SENHA é obrigatório',
            'password.string'           => 'O campo SENHA deve ser do tipo string',
            'password.regex'            => 'O campo SENHA deve conter de 8 a 20 caracteres( letras maiúsculas, minúsculas e números )',
            'confirm_password.required' => 'O campo CONFIRMAÇÂO DE SENHA é obrigatório',
            'confirm_password.string'   => 'O campo CONFIRMAÇÂO DE SENHA deve ser do tipo string',
            'confirm_password.same'     => 'As senhas digitadas não são iguais',
        ];
    }
}
