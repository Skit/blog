<?php
namespace blog\entities\relation\interfaces;

/**
 * Interface AsRelation
 * @package blog\entities\common\interfaces
 */
interface AsRelation
{
    public static function createForRelation(?AsRelation $object, string $field, $value): AsRelation;
}