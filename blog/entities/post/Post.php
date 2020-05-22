<?php declare(strict_types=1);

namespace blog\entities\post;

use blog\components\highlighter\HighlighterInterface;
use blog\entities\category\{Category, interfaces\CategoryInterface};
use blog\entities\common\abstracts\BlogRecordAbstract;
use blog\entities\common\exceptions\MetaDataExceptions;
use blog\entities\common\interfaces\ContentBundleInterface;
use blog\entities\common\{MetaData, Date};
use blog\entities\post\exceptions\PostBlogException;
use blog\entities\post\interfaces\PostInterface;
use blog\entities\relation\{interfaces\HasRelation, traits\HasRelationTrait};
use blog\entities\tag\TagBundle;
use blog\entities\user\User;
use Exception;
use Ramsey\Uuid\Uuid;

/**
 * Class Post
 * @package blog\entities\post
 */
class Post extends BlogRecordAbstract implements PostInterface, HasRelation
{
    use HasRelationTrait;

    public const STATUS_DRAFT = 3;
    // TODO добавить возможность доступка к статье только по ссылке
    public const ALLOW_BY_URL = 4;

    public const DEFAULT_COUNT_VIEW = 0;
    public const BANNER_TYPE_IMAGE = 1;
    public const BANNER_TYPE_VIDEO = 2;

    private $uuid;
    private $title;
    private $slug;
    /* @var PostBanners $post_banners */
    private $post_banners;
    private $preview;
    /* @var MetaData|string $meta_data */
    private $meta_data;
    /* @var  Category $category */
    private $category;
    /* @var $tagsBundle TagBundle */
    private $tags;
    /* @var $comments CommentBundle */
    private $comments;
    private $count_view;
    private $published_at;
    private $bannerType;
    private $isHighlight;
    private $highlighted_content;
    private $zip_content;

    /**
     * @param string $title
     * @param string $slug
     * @param PostBanners $mediaUrls
     * @param string $content
     * @param string $preview
     * @param MetaData $metaData
     * @param CategoryInterface $category
     * @param User $creator
     * @param string $published_at
     * @param int $status
     * @return Post
     * @throws PostBlogException
     */
    public static function create(string $title, string $slug, PostBanners $mediaUrls, string $content, string $preview,
                                  MetaData $metaData, CategoryInterface $category, User $creator, ?string $published_at, int $status)
    {
        try {
            $post = new self;
            $post->checkUserToActive($creator);

            $post->uuid = Uuid::uuid4()->toString();
            $post->title = $title;
            $post->slug = $slug;
            $post->content = $content;
            $post->preview = $preview;
            $post->meta_data = $metaData;
            $post->setCategory($category);
            $post->setPostBanners($mediaUrls);
            $post->user = $creator;
            $post->created_at = (new Date())->getFormatted();
            $post->published_at = (new Date($published_at))->getFormatted();
            $post->status = $status;
            $post->count_view = static::DEFAULT_COUNT_VIEW;
        } catch (Exception $e) {
            throw new PostBlogException("Fail to create post with: {$e->getMessage()}", 0, $e);
        }

        return $post;
    }

    /**
     * @param string $title
     * @param string $slug
     * @param PostBanners $mediaUrls
     * @param string $content
     * @param string $preview
     * @param MetaData $metaData
     * @param CategoryInterface $category
     * @param string $published_at
     * @param int $status
     * @throws PostBlogException
     */
    public function edit(string $title, string $slug, PostBanners $mediaUrls, string $content, string $preview,
                         MetaData $metaData, CategoryInterface $category, string $published_at, int $status)
    {
        try {
            $this->title = $title;
            $this->slug = $slug;
            $this->setPostBanners($mediaUrls);
            $this->content = $content;
            $this->preview = $preview;
            $this->setCategory($category);
            $this->meta_data = $metaData;
            $this->updated_at = (new Date())->getFormatted();
            // TODO если '' дата будет текущая
            $this->published_at = (new Date($published_at))->getFormatted();
            $this->status = $status;
        } catch (Exception $e) {
            throw new PostBlogException("Fail to update post with: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * @param CommentBundle $commentBundle
     * @throws PostBlogException
     */
    public function setComments(CommentBundle $commentBundle): void
    {
        if (!$this->isActive()) {
            throw new PostBlogException('Post must be active');
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
     * @throws PostBlogException
     */
    public function setBannerType(int $bannerType): void
    {
        switch ($bannerType) {
            case static::BANNER_TYPE_IMAGE:
                if (!$this->post_banners->hasImageUrl()) {
                    throw new PostBlogException('Post hasn`t an image url');
                }
                break;
            case static::BANNER_TYPE_VIDEO:
                if (!$this->post_banners->hasVideoUrl()) {
                    throw new PostBlogException('Post hasn`t a video url');
                }
                break;
            default:
                throw new PostBlogException('Unknown banner type');
                break;
        }

        $this->bannerType = $bannerType;
    }

    /**
     * @param CategoryInterface $category
     * @throws PostBlogException
     */
    public function setCategory(CategoryInterface $category)
    {
        if (!$category->isActive()) {
            throw new PostBlogException('Categories must be active');
        }

        $this->category = $category;
    }

    /**
     * @param ContentBundleInterface $tagBundle
     * @throws PostBlogException
     */
    public function setTags(ContentBundleInterface $tagBundle): void
    {
        if ($this->status === static::STATUS_DELETED) {
            throw new PostBlogException('Post must be active');
        }

        $this->tags = $tagBundle;
    }

    /**
     * @throws PostBlogException
     */
    public function draft(): void
    {
        if ($this->status === static::STATUS_DRAFT) {
            throw new PostBlogException('Post is already draft');
        }

        $this->status = static::STATUS_DRAFT;
    }

    /**
     * @param HighlighterInterface $highlighter
     * @throws PostBlogException
     */
    public function highlighting(HighlighterInterface $highlighter): void
    {
        $highlighter = $highlighter->highlighting($this->getContent());

        if ($highlighter->isHighlighted()) {
            $this->highlighted_content = $highlighter->getHighlighted();

            if (!$this->zip_content = gzcompress($this->content, 8)) {
                throw new PostBlogException('Failed to compress content data');
            }
        } else {
            $this->highlighted_content = $this->zip_content = null;
        }
    }

    /**
     * @return bool
     */
    public function isHighlight(): bool
    {
        return !empty($this->highlighted_content);
    }

    /**
     * @param PostBanners $post_banners
     * @throws PostBlogException
     */
    public function setPostBanners(PostBanners $post_banners): void
    {
        if (!$post_banners->hasImageUrl() && !$post_banners->hasVideoUrl()) {
            throw new PostBlogException('Post require one url of an image or a video');
        }

        $this->post_banners = $post_banners;
    }

    /**
     * @return mixed
     */
    public function getUuid(): string
    {
        return $this->uuid;
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
     * @return string|null
     */
    public function getContent(): ?string
    {
        if ($this->isHighlight() && $this->content === null) {
            $this->content = gzuncompress($this->getZipContent());
        }
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getHighlightContent(): ?string
    {
        return $this->highlighted_content;
    }

    /**
     * @return string|null
     */
    public function getZipContent(): ?string
    {
        return $this->zip_content;
    }


    /**
     * @return PostBanners
     * @throws PostBlogException
     */
    public function getBanners(): PostBanners
    {
        if (is_string($this->post_banners)) {
            $this->post_banners = PostBanners::fillByJson($this->post_banners);
        }

        return $this->post_banners;
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
     * @throws MetaDataExceptions
     */
    public function getMetaData(): MetaData
    {
        if (is_string($this->meta_data)) {
            $this->meta_data = MetaData::fillByJson($this->meta_data);
        }

        return $this->meta_data;
    }

    /**
     * @return mixed
     */
    public function getCategory(): Category
    {
        return $this->category;
    }

    /**
     * @return mixed
     */
    public function getCountView(): int
    {
        return (int) $this->count_view;
    }

    /**
     * @return TagBundle|null
     */
    public function getTags(): ?ContentBundleInterface
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
     * @deprecated
     * @see getComments
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
        return $this->published_at;
    }

    /**
     * @return mixed
     */
    public function getBannerType()
    {
        return $this->bannerType;
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