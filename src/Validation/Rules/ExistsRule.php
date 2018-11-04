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

class ExistsRule extends Rule
{
    protected $message = 'exists';

    protected $fillable_params = ['table', 'column'];

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

        // do query
        $stmt = $this->pdo->prepare("select exists(select 1 from {$table} where {$column} = ?)");
        $stmt->bindParam(1, $value);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_COLUMN);
    }
}
