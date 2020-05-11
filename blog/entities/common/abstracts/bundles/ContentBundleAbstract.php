<?php


namespace blog\entities\common\abstracts\bundles;


use blog\entities\common\exceptions\BundleExteption;
use blog\entities\common\interfaces\ContentBundleInterface;
use Closure;
use Exception;

/**
 * Class ContentBundleAbstract
 * @package blog\entities\common\abstracts
 */
abstract class ContentBundleAbstract implements ContentBundleInterface
{
    protected $count = 0;
    protected $bundle = [];

    /**
     * @return array
     */
    public function getBundle(): array
    {
        return $this->bundle;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param $item
     */
    public function append($item): void
    {
        $this->count++;
        $this->bundle[] = $item;
    }

    /**
     * Поменять местами параметры $bundle, $closure
     * @param Closure $closure
     * @param array $bundle
     * @throws BundleExteption
     */
    protected function createBundle(array $bundle, Closure $closure): void
    {
        try {
            foreach ($bundle as $item) {
                $this->append($closure->__invoke($item));
            }
        } catch (Exception $e) {
            throw new BundleExteption($e->getMessage(), 0, $e);
        }
    }
}