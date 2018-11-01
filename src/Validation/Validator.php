<?php

namespace Microparts\Igni\Support\Validation;

use Microparts\Igni\Support\Validation\Rules\BoolRule;
use Microparts\Igni\Support\Validation\Rules\StringRule;
use Microparts\Igni\Support\Validation\Rules\UniqueRule;
use Microparts\Igni\Support\Validation\Rules\UuidRule;

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
     * @param \PDO|null $pdo
     * @throws \Rakit\Validation\RuleQuashException
     */
    public function __construct(\PDO $pdo = null)
    {
        $this->validator = new \Rakit\Validation\Validator();
        $this->validator->addValidator('uuid', new UuidRule());
        $this->validator->addValidator('bool', new BoolRule());
        $this->validator->addValidator('string', new StringRule());
        $this->validator->addValidator('unique', new UniqueRule($pdo));
    }

    /**
     * @return \Rakit\Validation\Validator
     */
    public function getOriginalInstance(): \Rakit\Validation\Validator
    {
        return $this->validator;
    }

    /**
     * @param array $inputs
     * @param array $rules
     * @param array $messages
     * @throws \Microparts\Igni\Support\Validation\ValidationException
     */
    public function applyNow(array $inputs, array $rules, array $messages = [])
    {
        $validation = $this->validator->validate($inputs, $rules, $messages);

        if ($validation->fails()) {
            throw new ValidationException($validation->errors(), 422);
        }
    }
}
