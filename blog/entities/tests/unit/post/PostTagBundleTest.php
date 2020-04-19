<?php


namespace blog\entities\tests\unit\post;

use blog\entities\post\Post;
use blog\entities\tag\ArrayTagBundle;
use blog\entities\tag\TagBundle;
use Codeception\Stub;

/**
 * TODO починить тесты
 * Class PostTagBundleTest
 * @package blog\entities\tests\unit\post
 */
class PostTagBundleTest extends Base
{
    public function testSetTagBundleToActivePost()
    {
        /* @var $post Post */
        $post = Stub::make(Post::class, ['status' => Post::STATUS_ACTIVE]);
        $post->setTags(new ArrayTagBundle('first tag, second tag', 1));

        expect($post->getTags())->notEmpty();
        expect($post->getCountTags())->equals(2);
    }

    public function testSetTagBundleToInActivePost()
    {
        $post = Stub::make(Post::class, ['status' => Post::STATUS_INACTIVE]);

        $this->expectExceptionMessage('Post must be active');
        $post->setTags(new TagBundle([
            [
                'id' => 1,
                'title' => '1 title',
                'slug' => '1 title',
                'status' => 1,
            ],
        ]));
    }
}