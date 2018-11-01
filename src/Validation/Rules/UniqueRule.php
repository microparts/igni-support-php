<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 28/10/2018
 */

namespace Microparts\Igni\Support\Validation\Rules;

use PDO;
use Rakit\Validation\Rule;

class UniqueRule extends Rule
{
    protected $message = 'unique';

    protected $fillable_params = ['table', 'column', 'except'];

    protected $pdo;

    /**
     * UniqueRule constructor.
     *
     * @param \PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param $value
     * @return bool
     * @throws \Rakit\Validation\MissingRequiredParameterException
     */
    public function check($value)
    {
        // make sure required parameters exists
        $this->requireParameters(['table', 'column']);

        // getting parameters
        $column = $this->parameter('column');
        $table = $this->parameter('table');
        $except = $this->parameter('except');

        if ($except && $except === $value) {
            return true;
        }

        // do query
        $stmt = $this->pdo->prepare("select count(*) as count from `{$table}` where `{$column}` = :value");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        // true for valid, false for invalid
        return intval($data['count']) === 0;
    }
}
