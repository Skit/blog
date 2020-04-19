<?php
/*
 * TODO переделать, чтобы video и image => [url, default]. Убрать из таблицы поле 'баннера по умолчанию'.
 */
namespace blog\entities\post;

use blog\entities\post\exceptions\PostBlogException;
use Exception;
use JsonSerializable;

/**
 * Class MediaUrls
 * @package blog\entities\mediaUrls
 */
class PostBanners implements JsonSerializable
{
    private $imageUrl;
    private $videoUrl;

    /**
     * @param string $imageUrl
     * @param string $videoUrl
     * @return static
     */
    public static function create(string $imageUrl, string $videoUrl): self
    {
        $media = new self;
        $media->imageUrl = $imageUrl;
        $media->videoUrl = $videoUrl;

        return $media;
    }

    /**
     * TODO вынести в базовый класс
     * @param string|null $json
     * @return $this
     * @throws PostBlogException
     */
    public static function fillByJson(?string $json): self
    {
        if (!empty($json = json_decode($json, true))) {
            try {
                return self::create($json['imageUrl'], $json['videoUrl']);
            } catch (Exception $e) {
                throw new PostBlogException('Fail to set data from json', 0, $e);
            }
        }

        return new self();
    }

    /**
     * @return mixed
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @return mixed
     */
    public function getVideoUrl()
    {
        return $this->videoUrl;
    }

    public function setImageUrl(string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function setVideoUrl(string $videoUrl): self
    {
        $this->videoUrl = $videoUrl;
        return $this;
    }

    public function hasImageUrl(): bool
    {
        return !!$this->imageUrl;
    }

    public function hasVideoUrl(): bool
    {
        return !!$this->videoUrl;
    }

    /**
     * @return false|string
     */
    public function __toString()
    {
        //TODO JSON_THROW_ON_ERROR is available php >= 7.3 (JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        return json_encode($this,  JSON_UNESCAPED_UNICODE);
    }

    /**
     * TODO вынести в абстрактный клас и отнаследовать MetaData и остальных с такой потребностью
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return [
            'imageUrl' => $this->getImageUrl(),
            'videoUrl' => $this->getVideoUrl(),
        ];
    }
}