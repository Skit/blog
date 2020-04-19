<?php


namespace blog\entities\common;


use blog\entities\common\interfaces\ContentObjectInterface;
use blog\repositories\exceptions\RepositoryException;
use Closure;
use Exception;

/**
 * TODO перенести в папку repositories/common
 * TODO написать тест
 * Class RepositoryChecker
 * @package blog\entities
 */
class RepositoryChecker
{
    /**
     * @var int $executed
     */
    private $executed;

    /**
     * @var bool $executedResult
     */
    private $executedResult;

    /**
     * @param Closure $command
     * @return RepositoryChecker
     * @throws RepositoryException
     */
    public static function run(Closure $command): self
    {
        try {
            $checker = new self;
            $checker->executed = $command->__invoke();
        } catch (Exception $e) {
            throw new RepositoryException("Unable to execute: {$e->getMessage()}", $e->getCode(), $e);
        }

        return $checker;
    }

    /**
     * @param Closure $check
     * @return $this
     */
    public function if(Closure $check): self
    {
        $this->executedResult = $check->__invoke($this->executed);

        return $this;
    }

    /**
     * @param Exception $exception
     * @return RepositoryChecker
     * @throws Exception
     */
    public function throw(Exception $exception): self
    {
        if ($this->executedResult) {
            throw $exception;
        }

        return $this;
    }

    /**
     * @param Closure $closure
     * @return int|ContentObjectInterface
     */
    public function return(Closure $closure)
    {
        return $closure->__invoke($this->executed);
    }
}