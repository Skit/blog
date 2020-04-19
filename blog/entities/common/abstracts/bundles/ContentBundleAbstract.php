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
     * @param Closure $closure
     * @param array $bundle
     * @throws BundleExteption
     */
    protected function createBundle(Closure $closure, array $bundle): void
    {
        try {
            foreach ($bundle as $item) {
                $this->append($closure->__invoke($item));
            }
        } catch (Exception $e) {
            throw new BundleExteption($e->getMessage(), 0, $e);
        }
    }

    /**
     * @param string $field
     * @param string $quote
     * @param string $delimiter
     * @return string
     */
    public function getFieldsString(string $field, string $quote = '', string $delimiter = ','): string
    {
        $result = array_column($this->getBundle(), $field);

        if ($quote) {
            $result = array_map(function ($item) use ($quote) {
                return "{$quote}{$item}{$quote}";
            }, $result);
        }

        return implode($delimiter, $result);
    }
}