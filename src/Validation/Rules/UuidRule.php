<?php

namespace Microparts\Igni\Support\Validation\Rules;

use Ramsey\Uuid\Uuid;

/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 23/10/2018
 */

class UuidRule extends \Rakit\Validation\Rule
{
    /**
     * @var string
     */
    protected $message = 'must_uuid';

    /**
     * @param $value
     * @return bool
     */
    public function check($value)
    {
        return Uuid::isValid($value);
    }
}
