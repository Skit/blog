<?php declare(strict_types=1);

namespace blog\entities\relation;

use blog\entities\relation\exceptions\RelationException;
use blog\entities\relation\interfaces\AsRelation;

/**
 * TODO сделать описание
 * Class PrepareSql
 * @package blog\entities\common
 */
class RelationSql
{
    public const FIELD_DELIMITER = '__';
    public const PARENT_DELIMITER = '+';

    private $sql, $currentClass;

    /**
     * PrepareSql constructor.
     * @param $sql
     */
    public function __construct(string $sql)
    {
        $this->sql = preg_replace("~\s+~", ' ', $sql);
    }

    /**
     * @param string $fieldAlias
     * @return classSqlData|null
     */
    public static function getClassData(string $fieldAlias): ?classSqlData
    {
        $PD = static::PARENT_DELIMITER;
        $FD = static::FIELD_DELIMITER;
        $pattern = "~(?:(?<parent>\S+(?=\\{$PD}))\\{$PD})?(?<alias>\S+)(?={$FD}){$FD}(?<fieldName>\w+)~";

        if (preg_match($pattern, $fieldAlias, $m)) {
            $data = new classSqlData();
            $data->fieldName = $m['fieldName'];

            $data->className = static::repairClassName($m['alias']);
            $data->relationField = static::propertyNameFromClassName($data->className);

            if ($m['parent'] !== '') {
                $data->subParentFieldGetterName = 'get' . ucfirst($data->relationField);
                $data->parentClass = static::repairClassName($m['parent']);
                $data->parentField = static::propertyNameFromClassName($data->parentClass);
            }
        }

        return $data ?? null;
    }

    /**
     * @param string $sqlAlias
     * @return string
     */
    public static function repairClassName(string $sqlAlias): string
    {
        $FD = static::FIELD_DELIMITER;
        return preg_replace(['~/~', "~{$FD}\w+~"], ['\\', ''], $sqlAlias);
    }

    /**
     * @param string $className
     * @return string
     */
    public static function propertyNameFromClassName(string $className): string
    {
        return strtolower(
            preg_replace('~\S+(?=\\\)\\\~', '', $className)
        );
    }

    /**
     * @param string $alias
     * @param string $className
     * @return $this
     * @throws RelationException
     */
    public function withClass(string $alias, string $className): RelationSql
    {
        if (!method_exists($className, 'createForRelation')) {
            throw new RelationException("You must use AsRelation trait and implementing AsRelation in {$className}");
        }

        // TODO проверку на не существующий алиас. алиас может отсутствовать, тогда currentClass = null
        if (preg_match( "~(?:{$alias}\.\`?\w+\`?).*(?=from)~i", $this->sql, $selectSection)) {
            $selectTemplate = '{select}';
            $this->currentClass = str_replace('\\', '/', $className);

            $selectWithAlias = preg_replace_callback("~(?:{$alias}\.\`?(\w+)\`?)~", function ($match) {
                return $match[0] ." as '" . $this->currentClass . static::FIELD_DELIMITER . $match[1] . "'";
            }, $selectSection[0]);

            $sqlWithTemplate = str_replace($selectSection[0], $selectTemplate, $this->sql);
            $this->sql = str_replace($selectTemplate, $selectWithAlias, $sqlWithTemplate);
        }

        return $this;
    }

    /**
     * @param string $alias
     * @param string $className
     * @return $this
     */
    public function thatHas(string $alias, string $className)
    {
        $parent = $this->currentClass;
        $this->withClass($alias, $className);
        $this->sql = str_replace($this->currentClass, "{$parent}+{$this->currentClass}", $this->sql);

        return $this;
    }

    /**
     * @return string
     */
    public function getSql(): string
    {
        return $this->sql;
    }
}

/**
 * Class classSqlData
 * @package blog\entities\common
 */
class classSqlData
{
    /* @var $className AsRelation */
    public $className;

    /* @var $fieldName string */
    public $fieldName;

    /* @var $relationField string */
    public $relationField;

    /* @var $parentClass ?string */
    public $parentClass;

    /* @var $parentField ?string */
    public $parentField;

    /* @var $subParentFieldGetterName ?string */
    public $subParentFieldGetterName;

    /**
     * @return bool
     */
    public function hasParent(): bool
    {
        return $this->parentClass !== null;
    }
}