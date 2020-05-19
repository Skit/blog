<?php

use blog\entities\post\Comment;
use blog\entities\post\exceptions\CommentException;
use blog\entities\post\Text;
use blog\entities\tests\unit\comment\Base;
use Codeception\Stub;

class CommentEditTest extends Base
{
    public function testParent()
    {
        $this->specify('Without parent', function () {
            $comment = Stub::make(Comment::class, ['status' => Comment::STATUS_INACTIVE]);
            $comment->edit(new Text('Edit text'), null, Comment::STATUS_ACTIVE);

            expect($comment->getContent())->equals('Edit text');
            expect($comment->isActive())->true();
        });

        $this->specify('With parent', function () {
            $parent = Stub::make(Comment::class, ['hashCode' => uniqid(), 'status' => Comment::STATUS_ACTIVE]);
            $comment = Stub::make(Comment::class, ['hashCode' => uniqid()]);
            $comment->edit(new Text('Edit reply text'), $parent, Comment::STATUS_ACTIVE);

            expect($comment->isActive())->true();
            expect($comment->hasParent())->true();
            expect($comment->getContent())->equals('Edit reply text');
        });

        $this->specify('With inactive parent', function () {
            $parent = Stub::make(Comment::class);
            $comment = Stub::make(Comment::class, ['status' => Comment::STATUS_ACTIVE]);

            $this->expectExceptionMessage('Parent comment must be active');
            $comment->edit(new Text('Edit reply comment'), $parent, Comment::STATUS_ACTIVE);
        });

        $this->specify('Himself parent', function () {
            $comment = Comment::create(new Text('Comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);

            $this->expectExceptionMessage('Parent must be a different comment');
            $comment->edit(new Text('Edit'), $comment, Comment::STATUS_ACTIVE);
        });
    }

    public function testChild()
    {
        $this->specify('With active child', function () {
            /* @var $parent Comment */
            $parent = Stub::make(Comment::class, ['status' => Comment::STATUS_ACTIVE]);
            $comment = Comment::create(new Text('Comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);
            $comment->edit(new Text('Edit reply comment'), $parent, Comment::STATUS_ACTIVE);

            expect($parent->hasChild())->true();
            expect($parent->getChildren()[0]->getContent())->equals('Edit reply comment');
        });

        $this->specify('Inactive with active parent', function () {
            $parent = Stub::make(Comment::class, ['status' => Comment::STATUS_ACTIVE]);
            $comment = Stub::make(Comment::class, ['status' => Comment::STATUS_INACTIVE]);

            $this->expectExceptionMessage('Comment must be active to set parent');
            $comment->edit(new Text('Edit text'), $parent, Comment::STATUS_INACTIVE);
        });
    }

    public function testHashCode()
    {
        $comment = Comment::create(new Text('Comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);
        $hasCode = $comment->getHashCode();
        $comment->edit(new Text('New text'), null, Comment::STATUS_INACTIVE);

        expect($hasCode)->equals($comment->getHashCode());
    }

    public function testEditing()
    {
        $this->specify('Check dates', function () {
            $comment = Comment::create(new Text('Comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);
            $comment->edit(new Text('Comment'), null, Comment::STATUS_ACTIVE);

            expect($comment->getCreatedAt())->notNull();
            expect($comment->getUpdatedAt())->notNull();
            expect(strtotime($comment->getCreatedAt()))->lessOrEquals(strtotime($comment->getUpdatedAt()));
        });

        $this->specify('With many urls in content', function () {
            $comment = Comment::create(new Text('Comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);

            $spamText = file_get_contents(
                codecept_data_dir('spam_text.txt')
            );

            $this->expectExceptionMessage('Your comment looks like SPAM');
            $comment->edit(new Text($spamText), $comment, Comment::STATUS_ACTIVE);
        });
    }
}