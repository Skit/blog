<?php declare(strict_types=1);


namespace blog\services;

use blog\entities\assign\AssignBundle;
use blog\entities\common\abstracts\bundles\ObjectBundle;
use blog\entities\common\exceptions\BundleExteption;
use blog\entities\common\interfaces\ContentObjectInterface;

/**
 * Class AssignService
 * @package blog\services
 */
class AssignService
{
    /**
     * @param ObjectBundle $many
     * @param ContentObjectInterface $toObject
     * @param string $manyField
     * @param string $toField
     * @return AssignBundle
     * @throws BundleExteption
     */
    public function makeBundle(ObjectBundle $many, ContentObjectInterface $toObject, string $manyField, string $toField)
    {
        return new AssignBundle($many->getBundle(), $toObject->getPrimaryKey(), $toField, $manyField);
    }
}