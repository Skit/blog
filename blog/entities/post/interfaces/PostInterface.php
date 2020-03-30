<?php

namespace blog\entities\post\interfaces;

use blog\entities\post\CommentBundle;
use blog\entities\post\MediaUrls;
use blog\entities\tag\TagBundle;
use Closure;

/**
 * Interface PostInterface
 * @package blog\entities\post\interfaces
 */
interface PostInterface
{
    public function setMediaUrls(MediaUrls $mediaUrls): void;

    public function hasPreview(): bool;

    public function setComments(CommentBundle $commentBundle): void;

    public function setTags(TagBundle $tagBundle): void;

    public function setBannerType(int $bannerType): void;

    public function setHighlight(Closure $closure): void;

    public function isHighlight(): bool;

    public function getTags(): ?TagBundle;

    public function getComments(): ?CommentBundle;

    public function getCountComments(): int;

    public function getCountTags(): int;

    public function getCountView(): int;
}