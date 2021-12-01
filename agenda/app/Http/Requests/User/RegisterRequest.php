<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

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
     *  Filters to be applied to the input.
     *
     *  @return array
     */
    public function filters()
    {
        return [
            'after' => [
                'name'             => 'cast:string',
                'email'            => 'cast:string',
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
            'name'             => 'required|string',
            'email'            => 'required|string|email|unique:users',
            'password'         => 'required|string',
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
            'name.required'             => 'O campo NOME é obrigatório',
            'name.string'               => 'O campo NOME deve ser do tipo string',
            'email.required'            => 'O campo EMAIL é obrigatório',
            'email.string'              => 'O campo EMAIL deve ser do tipo string',
            'email.email'               => 'O campo deve ser do tipo EMAIL',
            'email.unique'              => 'Email já está sendo utilizado',
            'password.required'         => 'O campo SENHA é obrigatório',
            'password.string'           => 'O campo SENHA deve ser do tipo string',
            'confirm_password.required' => 'O campo CONFIRMAÇÂO DE SENHA é obrigatório',
            'confirm_password.string'   => 'O campo CONFIRMAÇÂO DE SENHA deve ser do tipo string',
            'confirm_password.same'     => 'As senhas digitadas não são iguais',
        ];
    }
}
