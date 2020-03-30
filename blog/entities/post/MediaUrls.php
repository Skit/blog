<?php

namespace blog\entities\post;

/**
 * Class MediaUrls
 * @package blog\entities\mediaUrls
 */
class MediaUrls
{
    private $imageUrl;
    private $videoUrl;

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
}