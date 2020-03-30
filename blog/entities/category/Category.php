<?php declare(strict_types=1);

namespace blog\entities\category;

use blog\entities\category\interfaces\CategoryInterface;
use blog\entities\common\abstracts\BlogRecordAbstract;
use blog\entities\common\Date;
use blog\entities\common\exceptions\BlogRecordsException;
use blog\entities\common\exceptions\MetaDataExceptions;
use blog\entities\common\MetaData;
use blog\entities\relation\interfaces\HasRelation;
use blog\entities\relation\traits\HasRelationTrait;
use blog\entities\user\User;

/**
 * Class Categories
 * @package blog\entities\category
 */
class Category extends BlogRecordAbstract implements CategoryInterface, HasRelation
{
    use HasRelationTrait;

    private $title;
    private $slug;
    private $meta_data;

    /**
     * @param string $title
     * @param string $slug
     * @param string $content
     * @param MetaData $metaData
     * @param User $creator
     * @param int $status
     * @return Category
     * @throws BlogRecordsException
     */
    public static function create(string $title, string $slug, string $content, MetaData $metaData, User $creator, int $status)
    {
        $category = new self;
        $category->checkUserToActive($creator);

        $category->meta_data = $metaData;
        $category->title = $title;
        $category->slug = $slug;
        $category->content = $content;
        $category->user = $creator;
        $category->createdAt = Date::getFormatNow();
        $category->status = $status;

        return $category;
    }

    /**
     * @param string $title
     * @param string $slug
     * @param string $content
     * @param MetaData $metaData
     * @param int $status
     */
    public function edit(string $title, string $slug, string $content, MetaData $metaData, int $status)
    {
        $this->title = $title;
        $this->slug = $slug;
        $this->content = $content;
        $this->meta_data = $metaData;
        $this->status = $status;
        $this->updatedAt = Date::getFormatNow();
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return (string) $this->title;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return (string) $this->slug;
    }

    /**
     * @return MetaData|null
     * @throws MetaDataExceptions
     */
    public function getMetaData(): ?MetaData
    {
        if (!$this->meta_data instanceof MetaData){
            $this->meta_data = MetaData::fillByJson($this->meta_data);
        }

        return $this->meta_data;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->setRelationObject($name, $value);
    }
}
