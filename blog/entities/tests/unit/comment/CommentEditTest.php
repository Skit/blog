<?php

use blog\entities\post\Comment;
use blog\entities\post\exceptions\CommentException;
use blog\entities\tests\unit\comment\Base;
use Codeception\Stub;

class CommentEditTest extends Base
{
    public function testWithoutParent()
    {
        $comment = Stub::make(Comment::class, ['status' => Comment::STATUS_INACTIVE]);
        $comment->edit('Edit text', null, Comment::STATUS_ACTIVE);

        expect($comment->getContent())->equals('Edit text');
        expect($comment->isActive())->true();
    }

    public function testWithParent()
    {
        $parent = Stub::make(Comment::class, ['hashCode' => uniqid(), 'status' => Comment::STATUS_ACTIVE]);
        $comment = Stub::make(Comment::class, ['hashCode' => uniqid('1_')]);
        $comment->edit('Edit reply text', $parent, Comment::STATUS_ACTIVE);

        expect($comment->getContent())->equals('Edit reply text');
        expect($comment->hasParent())->true();
        expect($comment->isActive())->true();
    }

    public function testWithParentInactive()
    {
        $parent = Stub::make(Comment::class);
        $comment = Stub::make(Comment::class, ['status' => Comment::STATUS_ACTIVE]);

        $this->expectExceptionMessage('Parent comment must be active');
        $comment->edit('Edit reply comment', $parent, Comment::STATUS_ACTIVE);
    }

    public function testInactiveWithActiveParent()
    {
        $parent = Stub::make(Comment::class, ['status' => Comment::STATUS_ACTIVE]);
        $comment = Stub::make(Comment::class, ['status' => Comment::STATUS_INACTIVE]);

        $this->expectExceptionMessage('Comment must be active to set parent');
        $comment->edit('Edit text', $parent, Comment::STATUS_INACTIVE);
    }

    public function testCommentHasOurParent()
    {
        $comment = Comment::create('Comment', $this->activeUser, null, Comment::STATUS_ACTIVE);

        $this->expectExceptionMessage('Parent must be a different comment');
        $comment->edit('Edit', $comment, Comment::STATUS_ACTIVE);
    }

    public function testSaveHashCode()
    {
        $comment = Comment::create( 'Comment', $this->activeUser, null, Comment::STATUS_ACTIVE);
        $hasCode = $comment->getHashCode();
        $comment->edit('New text', null, Comment::STATUS_INACTIVE);

        expect($hasCode)->equals($comment->getHashCode());
    }
}