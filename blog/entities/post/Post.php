<?php declare(strict_types=1);

namespace blog\entities\post;

use blog\entities\category\interfaces\CategoryInterface;
use blog\entities\common\abstracts\BlogRecordAbstract;
use blog\entities\common\Date;
use blog\entities\common\MetaData;
use blog\entities\post\exceptions\PostExceptionBlog;
use blog\entities\post\interfaces\PostInterface;
use blog\entities\tag\TagBundle;
use blog\entities\user\User;
use Closure;
use Exception;

/**
 * TODO закрыть свойства
 *
 * Class Post
 * @package blog\entities\post
 */
class Post extends BlogRecordAbstract implements PostInterface
{
    public const DEFAULT_COUNT_VIEW = 0;
    public const BANNER_TYPE_IMAGE = 1;
    public const BANNER_TYPE_VIDEO = 2;

    public const STATUS_DRAFT = 0;

    private $title;
    private $slug;
    /* @var MediaUrls $mediaUrls */
    private $mediaUrls;
    private $preview;
    /* @var MetaData $metaData */
    private $metaData;
    private $category;
    /* @var $comments TagBundle */
    private $tags;
    /* @var $comments CommentBundle */
    private $comments;
    private $countView;
    private $publishedAt;
    private $bannerType;
    private $isHighlight;

    /**
     * @param string $title
     * @param string $slug
     * @param MediaUrls $mediaUrls
     * @param string $content
     * @param string $preview
     * @param MetaData $metaData
     * @param CategoryInterface $category
     * @param User $creator
     * @param string $published_at
     * @param int $status
     * @return Post
     * @throws PostExceptionBlog
     */
    public static function create(string $title, string $slug, MediaUrls $mediaUrls, string $content, string $preview,
                                  MetaData $metaData, CategoryInterface $category, User $creator, string $published_at, int $status)
    {
        try {
            $post = new self;
            $post->checkUserToActive($creator);

            $post->title = $title;
            $post->slug = $slug;
            $post->content = $content;
            $post->preview = $preview;
            $post->metaData = $metaData;
            $post->setCategory($category);
            $post->setMediaUrls($mediaUrls);
            $post->user = $creator;
            $post->createdAt = (new Date())->getFormatted();
            $post->publishedAt = (new Date($published_at))->getFormatted();
            $post->status = $status;
            $post->countView = static::DEFAULT_COUNT_VIEW;
        } catch (Exception $e) {
            throw new PostExceptionBlog("Fail to create post with: {$e->getMessage()}", 0, $e);
        }

        return $post;
    }

    /**
     * @param string $title
     * @param string $slug
     * @param MediaUrls $mediaUrls
     * @param string $content
     * @param string $preview
     * @param MetaData $metaData
     * @param CategoryInterface $category
     * @param string $published_at
     * @param int $status
     * @throws PostExceptionBlog
     */
    public function edit(string $title, string $slug, MediaUrls $mediaUrls, string $content, string $preview,
                         MetaData $metaData, CategoryInterface $category, string $published_at, int $status)
    {
        try {
            $this->title = $title;
            $this->slug = $slug;
            $this->setMediaUrls($mediaUrls);
            $this->content = $content;
            $this->preview = $preview;
            $this->setCategory($category);
            $this->metaData = $metaData;
            $this->updatedAt = (new Date())->getFormatted();
            $this->publishedAt = (new Date($published_at))->getFormatted();
            $this->status = $status;
        } catch (Exception $e) {
            throw new PostExceptionBlog("Fail to update post with: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * @param CommentBundle $commentBundle
     * @throws PostExceptionBlog
     */
    public function setComments(CommentBundle $commentBundle): void
    {
        if (!$this->isActive()) {
            throw new PostExceptionBlog('Post must be active');
        }

        $this->comments = $commentBundle;
    }

    /**
     * @return bool
     */
    public function hasPreview(): bool
    {
        return !!$this->preview;
    }

    /**
     * TODO возможно стоит сделать объект banner
     * @param int $bannerType
     * @throws PostExceptionBlog
     */
    public function setBannerType(int $bannerType): void
    {
        switch ($bannerType) {
            case static::BANNER_TYPE_IMAGE:
                if (!$this->mediaUrls->hasImageUrl()) {
                    throw new PostExceptionBlog('Post hasn`t an image url');
                }
                break;
            case static::BANNER_TYPE_VIDEO:
                if (!$this->mediaUrls->hasVideoUrl()) {
                    throw new PostExceptionBlog('Post hasn`t a video url');
                }
                break;
            default:
                throw new PostExceptionBlog('Unknown banner type');
                break;
        }

        $this->bannerType = $bannerType;
    }

    /**
     * @param CategoryInterface $category
     * @throws PostExceptionBlog
     */
    public function setCategory(CategoryInterface $category)
    {
        if (!$category->isActive()) {
            throw new PostExceptionBlog('Categories must be active');
        }

        $this->category = $category;
    }

    /**
     * @param TagBundle $tagBundle
     * @throws PostExceptionBlog
     */
    public function setTags(TagBundle $tagBundle): void
    {
        if (!$this->isActive()) {
            throw new PostExceptionBlog('Post must be active');
        }

        $this->tags = $tagBundle;
    }

    /**
     * @throws PostExceptionBlog
     */
    public function draft(): void
    {
        if ($this->status === static::STATUS_DRAFT) {
            throw new PostExceptionBlog('Post is already draft');
        }

        $this->status = static::STATUS_DRAFT;
    }

    /**
     * @param Closure $closure
     */
    public function setHighlight(Closure $closure): void
    {
        $this->isHighlight = $closure->__invoke($this->content);
    }

    /**
     * @return bool
     */
    public function isHighlight(): bool
    {
        return $this->isHighlight;
    }

    /**
     * @param MediaUrls $mediaUrls
     * @throws PostExceptionBlog
     */
    public function setMediaUrls(MediaUrls $mediaUrls): void
    {
        if (!$mediaUrls->hasImageUrl() && !$mediaUrls->hasVideoUrl()) {
            throw new PostExceptionBlog('Post require one url of an image or a video');
        }

        $this->mediaUrls = $mediaUrls;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return MediaUrls
     */
    public function getMediaUrls(): MediaUrls
    {
        return $this->mediaUrls;
    }

    /**
     * @return mixed
     */
    public function getPreview()
    {
        return $this->preview;
    }

    /**
     * @return MetaData
     */
    public function getMetaData(): MetaData
    {
        return $this->metaData;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return mixed
     */
    public function getCountView(): int
    {
        return $this->countView;
    }

    /**
     * @return TagBundle|null
     */
    public function getTags(): ?TagBundle
    {
        return $this->tags;
    }

    /**
     * @return CommentBundle|null
     */
    public function getComments(): ?CommentBundle
    {
        return $this->comments;
    }

    /**
     * @return int
     */
    public function getCountComments(): int
    {
        return $this->comments->getCount();
    }

    /**
     * @return int
     */
    public function getCountTags(): int
    {
        return $this->tags->getCount();
    }

    /**
     * @return mixed
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @return mixed
     */
    public function getBannerType()
    {
        return $this->bannerType;
    }
}