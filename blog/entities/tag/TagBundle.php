<?php declare(strict_types=1);

namespace blog\entities\tag;


use blog\entities\common\abstracts\ContentBundleAbstract;
use blog\entities\tag\exceptions\TagException;
use Exception;

/**
 * Class TagBundle
 *
 * @property Tag[] $bundle
 * @package blog\entities\tag
 */
class TagBundle extends ContentBundleAbstract
{
    /**
     * TagBundle constructor.
     * @param array $tags
     * @throws TagException
     */
    public function __construct(array $tags)
    {
        $this->createBundle($tags);
    }

    /**
     * @param array $tags
     * @throws TagException
     */
    private function createBundle(array $tags): void
    {
        try {
            foreach ($tags as $tag) {
                $this->bundle[] = Tag::create($tag['id'], $tag['title'], $tag['slug'], $tag['status']);
                $this->count++;
            }
        } catch (Exception $e) {
            throw new TagException("Fail to create tag bundle with: {$e->getMessage()}", 0, $e);
        }
    }
}