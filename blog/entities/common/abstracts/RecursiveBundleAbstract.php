<?php


namespace blog\entities\common\abstracts;


use blog\entities\common\abstracts\bundles\ContentBundleAbstract;
use blog\entities\common\RecursiveContentBundle;
use RecursiveIterator;

/**
 * Class RecursiveBundleAbstract
 * @package blog\entities\common\abstracts
 */
abstract class RecursiveBundleAbstract extends ContentBundleAbstract implements RecursiveIterator
{
    /**
     * @inheritDoc
     */
    public function current()
    {
        return current($this->bundle);
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        return next($this->bundle);
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return key($this->bundle);
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return current($this->bundle) !== false;
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        reset($this->bundle);
    }

    /**
     * @inheritDoc
     */
    public function hasChildren()
    {
        return $this->current()->hasChild();
    }

    /**
     * @inheritDoc
     */
    public function getChildren()
    {
        return new RecursiveContentBundle($this->current()->getChildren());
    }
}