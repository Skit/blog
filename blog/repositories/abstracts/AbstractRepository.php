<?php declare(strict_types=1);

namespace blog\repositories\abstracts;

use blog\repositories\exceptions\RepositoryException;
use blog\repositories\interfaces\DAORepositoryInterface;
use common\components\MConnection;
use Exception;
use yii\db\Command;

/**
 * Class AbstractRepository
 * @property MConnection $dao
 *
 * @package blog\repositories\abstracts
 */
class AbstractRepository implements DAORepositoryInterface
{
    protected $dao;
    protected $className;

    /**
     * AbstractRepository constructor.
     * @param MConnection $dao
     */
    public function __construct(MConnection $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param Command $command
     * @return int
     * @throws RepositoryException
     */
    public function execute(Command $command): int
    {
        try {
            $rowAffected = $command->execute();
        } catch (Exception $exception) {
            throw new RepositoryException(
                "Unable to execute:\n{$exception->getMessage()}", 0, $exception);
        }

        return $rowAffected;
    }

    /**
     * @param Command $command
     * @param int $id
     * @return mixed
     * @throws RepositoryException
     */
    protected function fetchOne(Command $command)
    {
        if (!($record = $command->fetchObject($this->getClassName()))) {
            throw new RepositoryException("Record is not found");
        }

        return $record;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }
}