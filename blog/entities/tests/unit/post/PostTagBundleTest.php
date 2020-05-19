<?php


namespace blog\entities\tests\unit\post;

use blog\entities\post\Post;
use blog\entities\tag\ArrayTagBundle;
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
}