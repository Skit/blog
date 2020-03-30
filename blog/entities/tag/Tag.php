<?php declare(strict_types=1);

namespace blog\entities\tag;

use blog\entities\common\abstracts\ContentObjectAbstract;
use blog\entities\common\Date;
use blog\entities\tag\exceptions\TagException;
use Exception;

/**
 * Class Tag
 * @package blog\entities\tag
 */
class Tag extends ContentObjectAbstract
{
    private $title;
    private $slug;
    private $frequency;

    /**
     * @param int $id
     * @param string $title
     * @param string $slug
     * @param int $status
     * @return Tag
     * @throws TagException
     */
    public static function create(int $id, string $title, string  $slug, int $status)
    {
        try {
            $tag = new self;
            $tag->id = $id;
            $tag->title = $title;
            $tag->slug = $slug;
            $tag->frequency = 0;
            $tag->createdAt = (new Date())->getFormatted();
            $tag->status = $status;
        } catch (Exception $e) {
            throw new TagException("Fail to create the tag with: {$e->getMessage()}", 0, $e);
        }

        return $tag;
    }

    /**
     * @param string $title
     * @param string $slug
     * @param int $status
     * @throws TagException
     */
    public function edit(string $title, string  $slug, int $status)
    {
        try {
            $this->title = $title;
            $this->slug = $slug;
            $this->updatedAt = (new Date())->getFormatted();
            $this->status = $status;
        } catch (Exception $e) {
            throw new TagException("Fail to edit the tag with: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return integer
     */
    public function getFrequency(): int
    {
        return $this->frequency;
    }
}