<?php

use blog\entities\common\Date;
use blog\entities\post\Comment;
use blog\entities\post\CommentBundle;
use blog\entities\tests\unit\comment\Base;

/**
 * Class CommentBundleTest
 */
class CommentBundleTest extends Base
{
    public function testWithChild()
    {
        $comments = [
            [
                'id' => $parentId = $id = 1,
                'text' => 'Comment 1 parent',
                'creator' => $this->activeUser,
                'post' => $this->activePost,
                'parentComment' => null,
                'status' => Comment::STATUS_ACTIVE,
            ],
            [
                'id' => ++$id,
                'text' => 'Comment 2 reply to parent 1',
                'creator' => $this->activeUser,
                'post' => $this->activePost,
                'parentComment' => $parentId,
                'status' => Comment::STATUS_ACTIVE,
            ],
            [
                'id' => ++$id,
                'text' => 'Comment 3 reply to parent 1',
                'creator' => $this->activeUser,
                'post' => $this->activePost,
                'parentComment' => $parentId,
                'status' => Comment::STATUS_ACTIVE,
            ],
        ];

        $bundle = new CommentBundle($comments);

        expect($bundle->getBundle()[0]->getContent())->equals('Comment 1 parent');
        expect($bundle->getBundle()[0]->hasChild())->true();
        expect($bundle->getBundle()[0]->hasParent())->false();
        expect($bundle->getBundle()[0]->getChildren()[0]->getContent())->equals('Comment 2 reply to parent 1');
        expect($bundle->getBundle()[0]->getChildren()[1]->getContent())->equals('Comment 3 reply to parent 1');
    }

    public function testCreate()
    {
        $comments = [
            [
                'id' => 1,
                'text' => 'Comment 1 parent',
                'post' => $this->activePost,
                'creator' => $this->activeUser,
                'parentComment' => null,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
            [
                'id' => 2,
                'text' => 'Comment 2 reply to parent 1',
                'post' => $this->activePost,
                'creator' => $this->activeUser,
                'parentComment' => 1,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
            [
                'id' => 3,
                'text' => 'Comment 3 reply to child 2',
                'post' => $this->activePost,
                'creator' => $this->activeUser,
                'parentComment' => 2,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
        ];

        $bundle = new CommentBundle($comments);

        $this->specify('Get from bundle', function () use ($bundle) {
            $comment = $bundle->findByPrimaryKey(3);

            expect($comment->getPrimaryKey())->equals(3);
            expect($comment->getContent())->equals('Comment 3 reply to child 2');
        });

        $this->specify('Parent with child with child', function () use ($bundle) {
            expect($bundle->getCount())->equals(3);
            expect($bundle->getBundle()[0]->getContent())->equals('Comment 1 parent');
            expect($bundle->getBundle()[0]->hasChild())->true();
            expect($bundle->getBundle()[0]->hasParent())->false();

            expect($bundle->getBundle()[0]->getChildren()[0]->getContent())->equals('Comment 2 reply to parent 1');
            expect($bundle->getBundle()[0]->getChildren()[0]->hasChild())->true();
            expect($bundle->getBundle()[0]->getChildren()[0]->hasParent())->true();
            expect($bundle->getBundle()[0]->getChildren()[0]->getParent()->getContent())->equals('Comment 1 parent');

            expect($bundle->getBundle()[0]->getChildren()[0]->getChildren()[0]->getContent())->equals('Comment 3 reply to child 2');
            expect($bundle->getBundle()[0]->getChildren()[0]->getChildren()[0]->hasChild())->false();
            expect($bundle->getBundle()[0]->getChildren()[0]->getChildren()[0]->hasParent())->true();
            expect($bundle->getBundle()[0]->getChildren()[0]->getChildren()[0]->getParent()->getContent())->equals('Comment 2 reply to parent 1');
        });

        $this->specify('Delete from bundle', function () use ($bundle) {
            $comment = $bundle->findByPrimaryKey(3);

            expect($comment->isActive())->true();
            expect($bundle->removeByPrimaryKey(3))->true();
            expect($comment->isDelete())->true();
            expect($bundle->removeByPrimaryKey(8))->null();
        });
    }
}