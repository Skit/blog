<?php declare(strict_types=1);

namespace blog\entities\relation\traits;


use blog\entities\relation\interfaces\AsRelation;

/**
 * Trait AsRelationTrait
 * @package blog\entities\traits
 */
trait AsRelationTrait
{
    /**
     * @param AsRelation|null $object
     * @param string $field
     * @param $value
     * @return AsRelation
     */
    public static function createForRelation(?AsRelation $object, string $field, $value): AsRelation
    {
        $object = $object ?? new self;
        $object->{$field} = $value;

        return $object;
    }
}