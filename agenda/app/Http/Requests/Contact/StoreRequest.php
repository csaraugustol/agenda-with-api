<?php

namespace App\Http\Requests\Contact;

use App\Rules\UniqueContactName;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\SanitizesInput;

class StoreRequest extends FormRequest
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
                'name'     => 'cast:string',
                'tags'     => 'cast:array',
                'phones'   => 'cast:array',
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
            'name'                    => [
                'required',
                new UniqueContactName()
            ],
            'tags'                    => 'sometimes|array',
            'tags.*.id'               => 'sometimes|uuid',
            'phones'                  => 'required|array|min:1',
            'phones.*.phone_number'   => 'required|string',
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
            'name.required'                    => 'O campo NOME ?? obrigat??rio',
            'tags.*.id.uuid'                   => 'O campo ID da tag deve ser do tipo uuid',
            'phones.required'                  => '?? necess??rio informar o TELEFONE DO CONTATO',
            'phones.min'                       => '?? necess??rio informar pelo menos um telefone',
            'phones.*.phone_number.required'   => 'O campo N??MERO DO TELEFONE ?? obrigat??rio',
            'adresses.required'                => '?? necess??rio informar o ENDERE??O DO CONTATO',
            'adresses.min'                     => '?? necess??rio informar pelo menos um endere??o',
            'adresses.*.street_name.required'  => 'O campo NOME DA RUA ?? obrigat??rio',
            'adresses.*.number.required'       => 'O campo N??MERO ?? obrigat??rio',
            'adresses.*.number.numeric'        => 'O campo N??MERO deve conter valor n??merico',
            'adresses.*.neighborhood.required' => 'O campo BAIRRO ?? obrigat??rio',
            'adresses.*.city.required'         => 'O campo CIDADE ?? obrigat??rio',
            'adresses.*.state.required'        => 'O campo ESTADO ?? obrigat??rio',
            'adresses.*.state.max'             => 'O campo ESTADO deve conter no m??ximo 2 caracteres',
            'adresses.*.postal_code.required'  => 'O campo CEP ?? obrigat??rio',
            'adresses.*.postal_code.max'       => 'O campo CEP deve conter no m??ximo 9 caracteres',
            'adresses.*.country.required'      => 'O campo PA??S ?? obrigat??rio',
        ];
    }
}
