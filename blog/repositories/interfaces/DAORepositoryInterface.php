<?php


namespace blog\repositories\interfaces;


use common\components\MConnection;

interface DAORepositoryInterface
{
    public function __construct(MConnection $dao);

    public function getClassName(): string;
}