<?php


namespace blog\entities\common;


use blog\entities\common\abstracts\RecursiveBundleAbstract;
use blog\entities\common\interfaces\ContentObjectInterface;
use RecursiveIteratorIterator;

/**
 * Class RecursiveContentbundleIterator
 * @package blog\entities\common
 */
class RecursiveContentBundle extends RecursiveBundleAbstract
{
    /**
     * RecursiveContentBundle constructor.
     * @param array $bundle
     */
    public function __construct(array $bundle)
    {
        $this->bundle = $bundle;
    }

    /**
     * @param int $pk
     * @return ContentObjectInterface|null
     */
    public function findByPrimaryKey(int $pk): ?ContentObjectInterface
    {
        /* @var ContentObjectInterface $comment */
        foreach (new RecursiveIteratorIterator($this, RecursiveIteratorIterator::SELF_FIRST) as $comment) {
            if ($comment->getPrimaryKey() === $pk) {
                return $comment;
            }
        }

        return null;
    }
}