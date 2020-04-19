<?php declare(strict_types=1);

namespace blog\repositories\abstracts;

use blog\entities\common\interfaces\ContentObjectInterface;
use blog\entities\common\RepositoryChecker;
use blog\repositories\exceptions\RepositoryException;
use blog\repositories\interfaces\CRUDRepositoryInterface;
use Closure;
use PDO;

/**
 * Class AbstractRepository
 *
 * @package blog\repositories\abstracts
 */
abstract class AbstractRepository extends AbstractDAO implements CRUDRepositoryInterface
{
    /**
     * @var string $class
     */
    protected $class;
    /**
     * Name of database table
     * @var string $table
     */
    protected $table;

    /**
     * @param int $id
     * @param int $status
     * @return mixed
     */
    public function findOneById(int $id, int $status): ContentObjectInterface
    {
        return $this->dao
            ->createCommand("SELECT * FROM `{$this->table()}` WHERE `id`=:id AND `status`=:status LIMIT 1")
            ->bindValue(':id', $id, PDO::PARAM_INT)
            ->bindValue(':status', $status, PDO::PARAM_INT)
            ->fetchOneObject($this->getClassName());
    }

    /**
     * @param Closure $closure
     * @return RepositoryChecker
     * @throws RepositoryException
     */
    protected function checker(Closure $closure)
    {
        return RepositoryChecker::run($closure);
    }

    /**
     * @return string
     */
    protected function getClassName(): string
    {
        if (!$this->class) {
            throw new RepositoryException('Determine property $class in your repository class');
        }

        return $this->class;
    }

    /**
     * @return string
     */
    protected function table(): string
    {
        if (!$this->table) {
            throw new RepositoryException('Determine property $table in your repository class');
        }

        return $this->table;
    }
}