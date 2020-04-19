<?php


namespace blog\entities\common\abstracts\bundles;


use blog\entities\common\interfaces\ContentObjectInterface;

/**
 * Class ObjectBundle
 * @package blog\entities\common\abstracts\bundles
 */
abstract class ObjectBundle extends ContentBundleAbstract
{
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