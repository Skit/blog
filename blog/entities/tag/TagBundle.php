<?php declare(strict_types=1);

namespace blog\entities\tag;


use blog\entities\common\abstracts\bundles\ObjectBundle;
use blog\entities\tag\exceptions\TagException;
use Exception;

/**
 * TODO поменять исключение на BundleException
 * Class TagBundle
 *
 * @property Tag[] $bundle
 * @package blog\entities\tag
 */
class TagBundle extends ObjectBundle
{
    /**
     * TagBundle constructor.
     * @param array $tags
     * @throws TagException
     */
    public function __construct(array $tags = [])
    {
        try {
            $this->createBundle(function ($tag) {
                return Tag::createFull($tag['id'], $tag['title'], $tag['slug'], 0, '', null, $tag['status']);
            }, $tags);
        } catch (Exception $e) {
            throw new TagException("Fail to create tag bundle: {$e->getMessage()}", 0, $e);
        }
    }
}