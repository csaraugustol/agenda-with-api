<?php

namespace App\Http\Requests\ChangePassword;

use App\Rules\CheckUserPassword;
use App\Rules\CheckTokenToChangePassword;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\SanitizesInput;

class UpdatePasswordRequest extends FormRequest
{
    use SanitizesInput;

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
                'current_password'      => 'cast:string',
                'new_password'          => 'cast:string',
                'confirm_new_password'  => 'cast:string',
                'token_update_password' => 'cast:string',
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
            'current_password'     => 'required|string|',
            'new_password'         => 'required|string|regex:/^(?=.*[A-Z])(?=.*[0-9])(?=.*[a-z]).{8,20}$/',
            'confirm_new_password' => 'required|string|same:new_password',
            'token_update_password'                => 'required|string|',
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
            'current_password.required'      => 'O campo SENHA ATUAL é obrigatório',
            'current_password.string'        => 'O campo SENHA ATUAL deve ser do tipo string',
            'new_password.required'          => 'O campo NOVA SENHA é obrigatório',
            'new_password.string'            => 'O campo NOVA SENHA deve ser do tipo string',
            'new_password.regex'             => 'O campo NOVA SENHA deve conter de 8 a 20 caracteres( letras maiúsculas, minúsculas e números )',
            'confirm_new_password.required'  => 'O campo CONFIRMAÇÂO DE SENHA é obrigatório',
            'confirm_new_password.string'    => 'O campo CONFIRMAÇÂO DE SENHA deve ser do tipo string',
            'confirm_new_password.same'      => 'As senhas digitadas não são iguais',
            'token_update_password.required' => 'O campo TOKEN é obrigatório',
            'token_update_password.string'   => 'O campo TOKEN deve ser do tipo string',
        ];
    }
}
