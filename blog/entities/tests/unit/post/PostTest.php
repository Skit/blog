<?php
namespace blog\entities\tests\unit\post;

use blog\entities\category\Category;
use blog\entities\common\Date;
use blog\entities\common\MetaData;
use blog\entities\post\PostBanners;
use blog\entities\post\Post;
use blog\entities\user\User;
use Codeception\Stub;
use Codeception\Test\Unit;

/**
 * TODO зарефакторить
 * TODO разбить тест на файлы создание, редактирование...
 * TODO использовать Specify
 * TODO вторичные объекты создать в базовом классе
 * Class PostTest
 * @package blog\entities\tests\unit\post
 */
class PostTest extends Unit
{
    /* @var $activeUser User */
    private $activeUser;
    /* @var $activeCategory User */
    private $activeCategory;
    /* @var $hasImageMediaUrls PostBanners */
    private $hasImageMediaUrls;

    public function setUp(): void
    {
        $this->activeCategory = Stub::make(Category::class, ['isActive' => true]);
        $this->activeUser = Stub::make(User::class, ['username' => 'Bob' ,'status' => User::STATUS_ACTIVE, 'id' => 1]);
        $this->hasImageMediaUrls = Stub::make(PostBanners::class, ['hasImage' => true]);

        parent::setUp();
    }

    public function testCreateFull()
    {
        $metaData = new MetaData('Some seo title', 'Some seo desc', 'Some seo keys');
        $mediaUrls = PostBanners::create('https://some.image/url', 'https://some.video/url');

        $post = Post::create('Title', 'Slug', $mediaUrls, 'Some content',
            'Some preview',  $metaData, $this->activeCategory, $this->activeUser, Date::getFormatNow(), Post::STATUS_DRAFT);

        $post->setBannerType(Post::BANNER_TYPE_VIDEO);

        expect($post->getTitle())->equals('Title');
        expect($post->getBanners()->getImageUrl())->equals('https://some.image/url');
        expect($post->getBanners()->getVideoUrl())->equals('https://some.video/url');
        expect($post->getMetaData()->getTitle())->equals('Some seo title');
        expect($post->getMetaData()->getDescription())->equals('Some seo desc');
        expect($post->getMetaData()->getKeywords())->equals('Some seo keys');
        expect($post->getBannerType())->equals(Post::BANNER_TYPE_VIDEO);
    }

    public function testCreateWithInactiveCategory()
    {
        $category = Stub::make(Category::class, ['isActive' => false]);

        $this->expectExceptionMessage('Categories must be active');
        Post::create('Title', 'Slug', new PostBanners(), 'Some content',
            'Some preview', new MetaData(), $category, $this->activeUser, Date::getFormatNow(), Post::STATUS_DRAFT);
    }

    public function testCreateWithOutMediaUrls()
    {
        $this->expectExceptionMessage('Fail to create post with: Post require one url of an image or a video');
        Post::create('Title', 'Slug', new PostBanners(), 'Some content',
            'Some preview', new MetaData(), $this->activeCategory, $this->activeUser, Date::getFormatNow(), Post::STATUS_DRAFT);
    }

    public function testCreateWithInactiveUser()
    {
        $mediaUrls = Stub::make(PostBanners::class, ['hasImageUrl' => true]);

        $this->expectExceptionMessage('Fail to create post with: User must be active for this operation');
        Post::create('Title', 'Slug', $mediaUrls, 'Some content',
            'Some preview', new MetaData(), $this->activeCategory, new User(), Date::getFormatNow(), Post::STATUS_DRAFT);
    }

    public function testEdit()
    {
        $mediaUrls = PostBanners::create(null,  'https://some.video/url');
        $metaData = new MetaData('Some seo title', 'Some seo desc', 'Some seo keys');

        $post = Post::create('Title', 'Slug', $mediaUrls, 'Some content',
            'Some preview',  $metaData, $this->activeCategory, $this->activeUser, Date::getFormatNow(), Post::STATUS_DRAFT);

        $mediaUrls = PostBanners::create('https://edit.image/url', 'https://edit.video/url');
        $metaData = new MetaData('Edit seo title', 'Edit seo desc', 'Edit seo keys');

        $post->setBannerType(Post::BANNER_TYPE_VIDEO);

        $post->edit('Edit title', 'Edit slug', $mediaUrls, 'Edit content', 'Edit preview',
            $metaData, $this->activeCategory, Date::getFormatNow(), Post::STATUS_ACTIVE);

        $post->setBannerType(Post::BANNER_TYPE_IMAGE);

        expect($post->getTitle())->equals('Edit title');
        expect($post->getBanners()->getImageUrl())->equals('https://edit.image/url');
        expect($post->getBanners()->getVideoUrl())->equals('https://edit.video/url');
        expect($post->getMetaData()->getTitle())->equals('Edit seo title');
        expect($post->getMetaData()->getDescription())->equals('Edit seo desc');
        expect($post->getMetaData()->getKeywords())->equals('Edit seo keys');
        expect($post->getBannerType())->equals(Post::BANNER_TYPE_IMAGE);
    }

    public function testActivate()
    {
        $post = Stub::make(Post::class, ['status' => Post::STATUS_DRAFT]);

        $post->activate();
        expect($post->isActive())->true();
    }

    public function testImageUrl()
    {
        $post = Stub::make(Post::class, ['post_banners' => PostBanners::create('https://some.image/url', null)]);

        $this->expectExceptionMessage('Post hasn`t a video url');
        $post->setBannerType(Post::BANNER_TYPE_VIDEO);
        $post->setBannerType(Post::BANNER_TYPE_IMAGE);

        expect($post->getBannerType())->equals(Post::BANNER_TYPE_IMAGE);
    }

    public function testVideoUrl()
    {
        $post = Stub::make(Post::class, ['post_banners' => PostBanners::create(null, 'https://some.video/url')]);

        $this->expectExceptionMessage('Post hasn`t an image url');
        $post->setBannerType(Post::BANNER_TYPE_IMAGE);
        $post->setBannerType(Post::BANNER_TYPE_VIDEO);

        expect($post->getBannerType())->equals(Post::BANNER_TYPE_VIDEO);
    }

    public function testWithoutMetaData()
    {
        $post = Stub::make(Post::class, ['meta_data' => new MetaData()]);

        expect($post->getMetaData()->getTitle())->null();
        expect($post->getMetaData()->getDescription())->null();
        expect($post->getMetaData()->getDescription())->null();
    }

    public function testWrongActivate()
    {
        $post = Stub::make(Post::class, ['status' => Post::STATUS_ACTIVE]);

        $this->expectExceptionMessage('Record is already active');
        $post->activate();
    }

    public function testWrongDeactivate()
    {
        $post = Stub::make(Post::class, ['status' => Post::STATUS_INACTIVE]);

        $this->expectExceptionMessage('Record is already inactive');
        $post->deactivate();
    }

    public function testWrongDraft()
    {
        $post = Stub::make(Post::class, ['status' => Post::STATUS_DRAFT]);

        $this->expectExceptionMessage('Post is already draft');
        $post->draft();
    }

    public function testIsHighlightTrue()
    {
        $content = '<code class="php">Is highlight text</code>';
        /* @var $post  Post */
        $post = Stub::make(Post::class, [
            'content' => null,
            'highlighted_content' => $content,
            'zip_content' => gzcompress($content)]);

        expect($post->isHighlight())->true();
        expect($post->getContent())->equals($content);
    }

    public function testIsHighlightFalse()
    {
        /* @var $post  Post */
        $post = Stub::make(Post::class, ['content' => 'Is not highlight text']);

        expect($post->isHighlight())->false();
        expect($post->getZipContent())->null();
        expect($post->getHighlightContent())->null();
    }

}