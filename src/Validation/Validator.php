<?php

namespace Microparts\Support\Validation;

use Microparts\Support\Validation\Rules\BoolRule;
use Microparts\Support\Validation\Rules\StringRule;
use Microparts\Support\Validation\Rules\UuidRule;

/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 25/10/2018
 */

class Validator
{
    /**
     * @var \Rakit\Validation\Validator
     */
    private $validator;

    /**
     * Validator constructor.
     *
     * @throws \Rakit\Validation\RuleQuashException
     */
    public function __construct()
    {
        $this->validator = new \Rakit\Validation\Validator();
        $this->validator->addValidator('uuid', new UuidRule());
        $this->validator->addValidator('bool', new BoolRule());
        $this->validator->addValidator('string', new StringRule());
    }

    /**
     * @param array $inputs
     * @param array $rules
     * @param array $messages
     * @throws \Microparts\Support\Validation\ValidationException
     */
    public function applyNow(array $inputs, array $rules, array $messages = [])
    {
        $validation = $this->validator->validate($inputs, $rules, $messages);

        if ($validation->fails()) {
            throw new ValidationException($validation->errors(), 422);
        }
    }
}
