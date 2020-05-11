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
     * @throws Exception
     */
    public function run(Closure $closure): void
    {
        try {
            $this->dao->beginTransaction();
            $closure->__invoke();
            $this->dao->transaction->commit();
        } catch (Exception $e) {
            $this->dao->transaction->rollBack();
            throw $e;
        }
    }
}