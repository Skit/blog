<?php
namespace blog\entities\relation\interfaces;

/**
 * Class HasRelation
 * @package blog\entities\common\abstracts
 */
interface HasRelation
{
     public function __set($name, $value);
}