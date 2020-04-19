<?php declare(strict_types=1);

namespace common\components;

use Closure;
use PDO;
use yii\db\Command;
use yii\db\DataReader;
use yii\db\Exception;

/**
 * Class MCommand
 * @package common\components
 */
final class MCommand extends Command
{
    /**
     * @param string $class
     * @return mixed|DataReader
     * @throws Exception
     */
    public function fetchOneObject(string $class)
    {
        return $this->queryInternal('fetchObject', $class);
    }

    /**
     * @param Closure $closure
     * @return mixed|DataReader
     * @throws Exception
     */
    public function fetchAllObject(Closure $closure)
    {
        return $this->queryInternal('fetchAll', [PDO::FETCH_FUNC, $closure]);
    }
}