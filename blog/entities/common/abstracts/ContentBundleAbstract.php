<?php


namespace blog\entities\common\abstracts;


use blog\entities\common\interfaces\ContentBundleInterface;
use blog\entities\common\interfaces\ContentObjectInterface;

/**
 * Class ContentBundleAbstract
 * @package blog\entities\common\abstracts
 */
abstract class ContentBundleAbstract implements ContentBundleInterface
{
    protected $count = 0;
    protected $bundle = [];

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
}