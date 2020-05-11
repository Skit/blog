<?php declare(strict_types=1);


namespace blog\entities\assign;


use blog\entities\common\abstracts\bundles\ArrayBundle;
use blog\entities\common\exceptions\BundleExteption;
use Exception;

/**
 * Class AssignBundle
 * @package blog\entities\assign
 */
class AssignBundle extends ArrayBundle
{
    /**
     * AssignBundle constructor.
     * @param array $many
     * @param int $toPk
     * @param string $manyField
     * @param string $toField
     * @throws BundleExteption
     */
    public function __construct(array $many, int $toPk, string $toField, string $manyField)
    {
        try {
            $to = [$toField => $toPk];
            $this->createBundle($many, function ($item) use ($to, $manyField) {
                return array_merge($to, [$manyField => $item->getPrimaryKey()]);
            });
        } catch (Exception $e) {
            throw new BundleExteption("Fail to create tag bundle: {$e->getMessage()}", 0, $e);
        }
    }
}