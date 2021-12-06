<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'name'         => 'sometimes',
            'email'        => 'sometimes|email|unique:users',
        ];
    }

    /**
     * Retorna mensagens de erro durante a validação
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.email'  => 'O campo deve ser do tipo EMAIL',
            'email.unique' => 'Email já está sendo utilizado',
        ];
    }
}
