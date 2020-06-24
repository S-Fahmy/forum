<?php

namespace App\Rules;

use App\Inspections\Spam;
use Illuminate\Contracts\Validation\Rule;

class SpamFree implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value) //have a form, title = foobar, attribute is the title, value is foobar
    {

        try {

            $spam = new Spam();

          return ! $spam->detect($value); //if its spam it throws an exception to catch here
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'the :attribute contains spam!';
    }
}
