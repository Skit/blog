<?php declare(strict_types=1);


namespace common\components;

use Closure;
use Exception;

/**
 * Class MTransaction
 * @package common\components
 */
class MTransaction
{
    private $dao;

    /**
     * MTransaction constructor.
     * @param MConnection $dao
     */
    public function __construct(MConnection $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param Closure $closure
     * @return bool
     * @throws Exception
     */
    public function run(Closure $closure): bool
    {
        try {
            $this->dao->beginTransaction();
            $result = (bool) $closure->__invoke();
            $this->dao->transaction->commit();

            return $result;
        } catch (Exception $e) {
            $this->dao->transaction->rollBack();
            throw $e;
        }
    }
}