<?php


namespace blog\repositories\abstracts;


use common\components\MConnection;

/**
 * Class AbstractDAO
 * @property MConnection $dao
 *
 * @package blog\repositories\abstracts
 */
abstract class AbstractDAO
{
    /**
     * @var MConnection $dao
     */
    protected $dao;

    /**
     * AbstractRepository constructor.
     * @param MConnection $dao
     */
    public function __construct(MConnection $dao)
    {
        $this->dao = $dao;
    }
}