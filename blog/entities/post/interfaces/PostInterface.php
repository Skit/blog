<?php

namespace blog\entities\post\interfaces;

use blog\components\highlighter\HighlighterInterface;
use blog\entities\common\interfaces\ContentBundleInterface;
use blog\entities\post\CommentBundle;
use blog\entities\post\PostBanners;

/**
 * Interface PostInterface
 * @package blog\entities\post\interfaces
 */
interface PostInterface
{
    public function setPostBanners(PostBanners $mediaUrls): void;

    public function hasPreview(): bool;

    public function setComments(CommentBundle $commentBundle): void;

    public function setTags(ContentBundleInterface $tagBundle): void;

    public function setBannerType(int $bannerType): void;

    public function highlighting(HighlighterInterface $highlighter): void;

    public function isHighlight(): bool;

    public function getUuid(): string;

    public function getHighlightContent(): ?string;

    public function getZipContent(): ?string;

    public function getTags(): ?ContentBundleInterface;

    public function getComments(): ?CommentBundle;

    public function getCountComments(): int;

    public function getCountTags(): int;

    public function getCountView(): int;
}