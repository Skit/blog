<?php


namespace blog\entities\common\abstracts\bundles;


use blog\entities\common\exceptions\BundleExteption;
use blog\entities\common\interfaces\ContentObjectInterface;
use Closure;

/**
 * Class ObjectBundle
 * @package blog\entities\common\abstracts\bundles
 */
abstract class ObjectBundle extends ContentBundleAbstract
{
    /**
     * ObjectBundle constructor.
     * @param array $items
     * @param Closure|null $closure
     * @throws BundleExteption
     */
    public function __construct(array $items, Closure $closure)
    {
        $this->createBundle($items, $closure);
    }

    /**
     * @param string $field
     * @param string $quote
     * @param string $delimiter
     * @return string
     */
    public function getFieldsString(string $field, string $quote = '', string $delimiter = ','): string
    {
        foreach ($this->getBundle() as $item) {
            $item = call_user_func([$item, 'get' . ucfirst($field)]);
            $result[] = $quote ? "{$quote}{$item}{$quote}" : $item;
        }

        return implode($delimiter, $result ?? []);
    }

    /**
     * @param int $pk
     * @return bool
     */
    public function removeByPrimaryKey(int $pk): ?bool
    {
        foreach ($this->bundle as $key => $item) {
            if ($item->getPrimaryKey() === $pk) {
                $result = true;
                $this->count--;
                unset($this->bundle[$key]);
                break;
            }
        }

        return $result ?? false;
    }

    /**
     * @param int $pk
     * @return ContentObjectInterface|null
     */
    public function findByPrimaryKey(int $pk): ?ContentObjectInterface
    {
        /* @var ContentObjectInterface $item */
        foreach ($this->bundle as $item) {
            if ($item->getPrimaryKey() === $pk) {
                return $item;
            }
        }

        return null;
    }
}