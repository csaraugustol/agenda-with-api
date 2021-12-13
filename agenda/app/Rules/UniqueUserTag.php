<?php

namespace App\Rules;

use App\Models\Tag;
use Illuminate\Contracts\Validation\Rule;

class UniqueUserTag implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $tag = Tag::where([
            ['description', $value],
            ['user_id', user('id')],
        ])->get();

        return $tag->isEmpty();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Já existe uma tag com este nome.';
    }
}
