<?php

namespace App\Http\Requests\Tag;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\SanitizesInput;

class AttachOrDetachRequest extends FormRequest
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
                'contact_id' => 'cast:string',
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
            'contact_id' => 'required|uuid',
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
            'contact_id.required' => 'O campo ID DO CONTATO é obrigatório',
            'contact_id.uuid' => 'O campo ID DO CONTATO deve ser do tipo UUID',
        ];
    }
}
