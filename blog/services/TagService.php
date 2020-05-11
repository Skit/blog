<?php


namespace blog\services;


use blog\entities\tag\ArrayTagBundle;
use blog\entities\tag\exceptions\TagException;
use blog\entities\tag\Tag;
use blog\entities\tag\TagBundle;

/**
 * Class TagService
 * @package blog\services
 */
class TagService
{
    /**
     * @param string $tags
     * @return ArrayTagBundle
     * @throws TagException
     */
    public function createBundleFromString(string $tags): ArrayTagBundle
    {
        return new ArrayTagBundle($tags, Tag::STATUS_ACTIVE);
    }

    /**
     * @param ArrayTagBundle $bundle
     * @return TagBundle
     * @throws TagException
     */
    public function arrayBundleToObject(ArrayTagBundle $bundle): TagBundle
    {
        return new TagBundle($bundle->getBundle(), function ($item) {
            return Tag::create($item['title'], $item['slug'], $item['status']);
        });
    }

}