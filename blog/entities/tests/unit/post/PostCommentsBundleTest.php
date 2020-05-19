<?php


namespace blog\entities\tests\unit\post;

use blog\entities\common\Date;
use blog\entities\post\Comment;
use blog\entities\post\CommentBundle;
use blog\entities\post\Post;
use Codeception\Stub;

/**
 * Class PostCommentsBundleTest
 * @package blog\entities\tests\unit\post
 */
class PostCommentsBundleTest extends Base
{
    public function testSetCommentsBundleToActivePost()
    {
        /* @var $post Post */
        $post = Stub::make(Post::class, ['status' => Post::STATUS_ACTIVE]);
        $post->setComments(new CommentBundle([
            // TODO вынести в общие данные
            [
                'id' => 1,
                'text' => 'Comment 1 parent',
                'post' => $post,
                'creator' => $this->activeUser,
                'parentComment' => null,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
            [
                'id' => 12,
                'text' => 'Comment 2 reply to parent 1',
                'post' => $post,
                'creator' => $this->activeUser,
                'parentComment' => 1,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
            [
                'id' => 32,
                'text' => 'Comment 3 reply to parent 1',
                'post' => $post,
                'creator' => $this->activeUser,
                'parentComment' => 1,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
        ]));

        expect($post->getCountComments())->equals(3);
        expect($post->getComments())->isInstanceOf(CommentBundle::class);
    }

    public function testSetCommentsBundleToInActivePost()
    {
        /* @var $post Post */
        $post = Stub::make(Post::class, ['status' => Post::STATUS_INACTIVE]);

        $this->expectExceptionMessage('must be active for this operation');
        $post->setComments(new CommentBundle([
            [
                'id' => 1,
                'text' => 'Comment 1 parent',
                'post' => $post,
                'creator' => $this->activeUser,
                'parentComment' => null,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ]
        ]));
    }
}