<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\SanitizesInput;

class UpdateRequest extends FormRequest
{
    //use SanitizesInput;

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
                'street_name'  => 'cast:string',
                'number'       => 'cast:numeric',
                'complement'   => 'cast:string',
                'neighborhood' => 'cast:string',
                'city'         => 'cast:string',
                'state'        => 'cast:string',
                'postal_code'  => 'cast:string',
                'country'      => 'cast:string',
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
            'street_name'  => 'sometimes',
            'number'       => 'sometimes|numeric',
            'complement'   => 'sometimes',
            'neighborhood' => 'sometimes',
            'city'         => 'sometimes',
            'state'        => 'sometimes',
            'postal_code'  => 'sometimes',
            'country'      => 'sometimes',
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
            'number.numeric' => 'O campo NÙMERO deve ser do tipo numérico.',
        ];
    }
}
