<?php

use blog\entities\post\Comment;
use blog\entities\post\Text;
use blog\entities\tests\unit\comment\Base;
use Codeception\Stub;

/**
 * Class CommentCreateTest
 */
class CommentCreateTest extends Base
{
    public function testWithPost()
    {
        $this->specify('Check dates', function () {
            $comment = Comment::create(new Text('Comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);

            verify($comment->getUpdatedAt())->null();
            verify($comment->getCreatedAt())->notNull();
        });

        $this->specify('Active post', function () {
            $comment = Comment::create(new Text('Comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);

            verify($comment->isActive())->true();
            verify($comment->hasParent())->false();
            verify($comment->hasChild())->false();
            verify($comment->getPost()->isActive())->true();
            verify($comment->getContent())->equals('Comment');
        });

        $this->specify('Inactive post', function () {
            $this->expectExceptionMessage('must be active for this operation');
            Comment::create(new Text('Comment'), Comment::STATUS_ACTIVE, null, $this->inactivePost, $this->activeUser);
        });
    }

    public function testWithUser()
    {
        $this->specify('Inactive user', function () {
            $comment =Comment::create(new Text('Comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);

            verify($comment->getCreator()->isActive())->true();
        });

        $this->specify('Inactive user', function () {
            $this->expectExceptionMessage('must be active for this operation');
            Comment::create(new Text('Comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->inactiveUser);
        });
    }

    public function testParent()
    {
        $this->specify('Without parent', function () {
            $comment = Comment::create(new Text('Comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);
            expect($comment->hasParent())->false();
        });

        $this->specify('With active', function () {
            $comment = Comment::create(new Text('Comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);
            $child = Stub::make(Comment::class, ['parentComment' => $comment, 'status' => Comment::STATUS_ACTIVE]);

            expect($child->hasParent())->true();
            expect($child->getParent()->getContent())->equals('Comment');
        });

        $this->specify('Parent has child', function () {
            $comment = Comment::create(new Text('Comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);
            Comment::create(new Text('Reply comment'), Comment::STATUS_ACTIVE, $comment, $this->activePost, $this->activeUser);

            expect($comment->hasChild())->true();
        });

        $this->specify('Set with inactive comment', function () {
            $comment = Comment::create(new Text('Comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);

            $this->expectExceptionMessage('Comment must be active to set parent');
            Comment::create( new Text('Comment'), Comment::STATUS_INACTIVE, $comment, $this->activePost,  $this->activeUser);
        });

        $this->specify('With inactive', function () {
            $comment = Comment::create(new Text('Comment'), Comment::STATUS_INACTIVE, null, $this->activePost, $this->activeUser);

            $this->expectExceptionMessage('Parent comment must be active');
            Comment::create(
                new Text('Reply comment'),
                Comment::STATUS_ACTIVE,
                $comment,
                $this->activePost,
                $this->activeUser);
        });
    }

    public function testChild()
    {
        $this->specify('Has not child', function () {
            $comment = Comment::create(new Text('Comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);

            expect($comment->hasChild())->false();
        });

        $this->specify('Parent has child', function () {
            $comment = Comment::create(new Text('Comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);
            Comment::create(new Text('Reply comment'), Comment::STATUS_ACTIVE, $comment, $this->activePost, $this->activeUser);

            expect($comment->hasChild())->true();
        });

        $this->specify('Parent has child', function () {
            $comment = Comment::create(new Text('Comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);
            Comment::create(new Text('Reply comment'), Comment::STATUS_ACTIVE, $comment, $this->activePost, $this->activeUser);

            expect($comment->hasChild())->true();
        });

        $this->specify('Has many child', function () {
            $parent = Comment::create(new Text('Parent comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);
            Comment::create(new Text('Reply comment one'), Comment::STATUS_ACTIVE, $parent, $this->activePost, $this->activeUser);
            Comment::create(new Text('Reply comment two'), Comment::STATUS_ACTIVE, $parent, $this->activePost, $this->activeUser);

            expect($parent->getChildren())->count(2);
            expect($parent->getChildren()[0]->getContent())->stringContainsString('Reply comment');
            expect($parent->getChildren()[0]->hasParent())->true();
            expect($parent->getChildren()[1]->getContent())->stringContainsString('Reply comment');
            expect($parent->getChildren()[1]->hasParent())->true();
        });

        $this->specify('Child parent child', function () {
            $parent = Comment::create(new Text('Parent comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);
            $child = Comment::create(new Text('Child parent comment'), Comment::STATUS_ACTIVE, $parent, $this->activePost, $this->activeUser);
            $replyChild = Comment::create(new Text('Reply to child comment'), Comment::STATUS_ACTIVE, $child, $this->activePost, $this->activeUser);

            expect($parent->hasParent())->false();
            expect($parent->hasChild())->true();
            expect($parent->getChildren()[0]->getContent())->equals('Child parent comment');

            expect($child->hasParent())->true();
            expect($child->hasChild())->true();
            expect($child->getParent()->getContent())->equals('Parent comment');
            expect($child->getChildren()[0]->getContent())->equals('Reply to child comment');

            expect($replyChild->hasParent())->true();
            expect($replyChild->hasChild())->false();
            expect($replyChild->getParent()->getContent())->equals('Child parent comment');
        });
    }

    public function testCommentWithManyUrls()
    {
        $spamText = file_get_contents(
            codecept_data_dir('spam_text.txt')
        );

        $this->expectExceptionMessage('Your comment looks like SPAM');
        Comment::create(new Text($spamText), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);
    }

    public function testCompareComments()
    {
        $comment1 = Comment::create(new Text('Comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);
        $comment2 = Comment::create(new Text('Comment'), Comment::STATUS_ACTIVE, null, $this->activePost, $this->activeUser);

        expect($comment1->getHashCode() === $comment2->getHashCode())->false();
    }
}