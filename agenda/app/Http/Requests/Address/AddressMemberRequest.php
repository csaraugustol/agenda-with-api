<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\SanitizesInput;

class AddressMemberRequest extends FormRequest
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
                'adresses' => 'cast:array',
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
            'adresses'                => 'required|array|min:1',
            'adresses.*.street_name'  => 'required|string',
            'adresses.*.number'       => 'required|numeric',
            'adresses.*.complement'   => 'sometimes|string',
            'adresses.*.neighborhood' => 'required|string',
            'adresses.*.city'         => 'required|string',
            'adresses.*.state'        => 'required|string|max:2',
            'adresses.*.postal_code'  => 'required|string|max:9',
            'adresses.*.country'      => 'required|string',
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
            'adresses.required'                => 'É necessário informar o ENDEREÇO DO CONTATO',
            'adresses.min'                     => 'É necessário informar pelo menos um endereço',
            'adresses.*.street_name.required'  => 'O campo NOME DA RUA é obrigatório',
            'adresses.*.number.required'       => 'O campo NÚMERO é obrigatório',
            'adresses.*.number.numeric'        => 'O campo NÚMERO deve conter valor númerico',
            'adresses.*.neighborhood.required' => 'O campo BAIRRO é obrigatório',
            'adresses.*.city.required'         => 'O campo CIDADE é obrigatório',
            'adresses.*.state.required'        => 'O campo ESTADO é obrigatório',
            'adresses.*.state.max'             => 'O campo ESTADO deve conter no máximo 2 caracteres',
            'adresses.*.postal_code.required'  => 'O campo CEP é obrigatório',
            'adresses.*.postal_code.max'       => 'O campo CEP deve conter no máximo 9 caracteres',
            'adresses.*.country.required'      => 'O campo PAÍS é obrigatório',
        ];
    }
}
