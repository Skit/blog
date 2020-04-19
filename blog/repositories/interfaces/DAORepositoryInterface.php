<?php


namespace blog\repositories\interfaces;


use common\components\MConnection;

/**
 * @deprecated
 * Interface DAORepositoryInterface
 * @package blog\repositories\interfaces
 */
interface DAORepositoryInterface
{
    public function __construct(MConnection $dao);

    public function getClassName(): string;
}