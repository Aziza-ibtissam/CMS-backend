<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AcademicEmail implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Validate email address against academic criteria
        return preg_match('/@univ.*(\.edu|\.dz)$/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be an academic email ending with .edu or .dz and containing "@univ".';
    }
}
