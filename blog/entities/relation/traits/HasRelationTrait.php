<?php declare(strict_types=1);

namespace blog\entities\relation\traits;

use blog\entities\relation\RelationSql;

/**
 * Trait HasRelationTrait
 * @package blog\entities\traits
 */
trait HasRelationTrait
{
    /**
     * TODO описание
     * @param $name
     * @param $value
     */
    public function setRelationObject(string $name, $value): void
    {
        if ($value !== null && strrpos($name, RelationSql::FIELD_DELIMITER) > 0) {
            // TODO сделать выброс исключения
            $relation = RelationSql::getClassData($name);

            // filling parent relation object
            if ($relation->hasParent()) {
                if($this->{$relation->parentField} === null) {
                    $this->{$relation->parentField} = $relation->parentClass::createForRelation(
                        $this->{$relation->parentField}, $relation->relationField, null
                    );
                }

                // TODO кинуть исключение если не найдет геттер
                $subRelationObject = $relation->className::createForRelation(
                    $this->{$relation->parentField}->{$relation->subParentFieldGetterName}(), $relation->fieldName, $value
                );

                $this->{$relation->parentField} = $relation->parentClass::createForRelation(
                    $this->{$relation->parentField}, $relation->relationField, $subRelationObject
                );
            }
            // filling relation object
            else {
                $this->{$relation->relationField} = $relation->className::createForRelation(
                    $this->{$relation->relationField}, $relation->fieldName, $value
                );
            }
        }
    }
}