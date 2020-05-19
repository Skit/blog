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
     * TODO сделать параметры объектами как в комментариях
     * @param int|null $pk
     * @param string $title
     * @param string $slug
     * @param int $frequency
     * @param string $created_at
     * @param string|null $updated_at
     * @param int $status
     * @return Tag
     * @throws Exception
     */
    public static function createFull(?int $pk, string $title, string $slug, ?int $frequency,
                                      ?string $created_at, ?string $updated_at, int $status)
    {
        $tag = new self;
        $tag->id = $pk;
        $tag->title = $title;
        $tag->slug = $slug;
        $tag->frequency = $frequency;
        $tag->created_at = $created_at ?? (new Date())->getFormatted();
        $tag->updated_at = $updated_at;
        $tag->status = $status;

        return $tag;
    }

    /**
     * @param string $title
     * @param string $slug
     * @param int $status
     * @return Tag
     * @throws TagException
     */
    public static function create(string $title, string  $slug, int $status): Tag
    {
        try {
            $tag = self::createFull(null, $title, $slug, null, null, null, $status);
        } catch (Exception $e) {
            throw new TagException("Fail to create the tag with: {$e->getMessage()}", 0, $e);
        }

        return $tag;
    }

    /**
     * @param string $title
     * @param string $slug
     * @param int $status
     *
     * @throws TagException
     */
    public function edit(string $title, string  $slug, int $status): void
    {
        try {
            $this->title = $title;
            $this->slug = $slug;
            $this->updated_at = (new Date())->getFormatted();
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
        return (int) $this->frequency;
    }
}