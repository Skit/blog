<?php

namespace blog\repositories\abstracts;

/**
 * Class AbstractAssignRepository
 */
abstract class AbstractAssignRepository extends AbstractRepository
{
    protected $toField;
    protected $manyField;

    /**
     * @return string
     */
    public function getToField(): string
    {
        return $this->toField;
    }

    /**
     * @return string
     */
    public function getManyField(): string
    {
        return $this->manyField;
    }
}