<?php

namespace Microparts\Support\Validation\Rules;

/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 23/10/2018
 */

class StringRule extends \Rakit\Validation\Rule
{
    /**
     * @var string
     */
    protected $message = 'must_string';

    /**
     * @param $value
     * @return bool
     */
    public function check($value)
    {
        return is_string($value);
    }
}
