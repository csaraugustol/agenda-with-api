<?php

namespace App\Http\Requests\Vexpenses;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\SanitizesInput;

class AccessTokenRequest extends FormRequest
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
                'token'                      => 'cast:string',
                'system'                     => 'cast:string',
                'expires_at'                 => 'cast:bool',
                'clear_rectroativics_tokens' => 'cast:bool',
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
            'token'                      => 'required|string',
            'system'                     => 'required|string',
            'expires_at'                 => 'required|bool',
            'clear_rectroativics_tokens' => 'required|bool',
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
            'token.required'                      => 'O campo TOKEN é obrigatório',
            'token.string'                        => 'O campo TOKEN deve ser do tipo string',
            'system.required'                     => 'O campo TIPO DO SISTEMA é obrigatório',
            'system.string'                       => 'O campo TIPO DO SISTEMA deve ser do tipo string',
            'expires_at.required'                 => 'O campo EXPIRAÇÂO é obrigatório',
            'expires_at.bool'                     => 'O campo EXPIRAÇÂO deve ser do tipo boleano',
            'clear_rectroativics_tokens.required' => 'O campo LIMPAR TOKENS RETROATIVOS é obrigatório',
            'clear_rectroativics_tokens.bool'     => 'O campo LIMPAR TOKENS RETROATIVOS deve ser do tipo boleano',
        ];
    }
}
