<?php

namespace App\Http\Requests\Contact;

use App\Rules\UniqueContactName;
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
                'name' => 'cast:string',
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
            'name' => [
                'sometimes',
                new UniqueContactName()
            ],
        ];
    }
}
