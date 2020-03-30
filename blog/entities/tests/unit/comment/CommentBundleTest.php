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
    public function testCreateBundle()
    {
        $comments = [
            [
                'id' => 1,
                'text' => 'Comment 1',
                'creator' => $this->activeUser,
                'parentComment' => null,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
            [
                'id' => 2,
                'text' => 'Comment 2',
                'creator' => $this->activeUser,
                'parentComment' => null,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ]
        ];

        $bundle = new CommentBundle($comments);

        expect($bundle->getCount())->equals(2);
        expect($bundle->getBundle()[0]->getContent())->equals('Comment 1');
        expect($bundle->getBundle()[1]->getContent())->equals('Comment 2');
    }

    public function testCreateParentWithChild()
    {
        $comments = [
            [
                'id' => 1,
                'text' => 'Comment 1 parent',
                'creator' => $this->activeUser,
                'parentComment' => null,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
            [
                'id' => 12,
                'text' => 'Comment 2 reply to parent 1',
                'creator' => $this->activeUser,
                'parentComment' => 1,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
            [
                'id' => 32,
                'text' => 'Comment 3 reply to parent 1',
                'creator' => $this->activeUser,
                'parentComment' => 1,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
        ];

        $bundle = new CommentBundle($comments);

        expect($bundle->getCount())->equals(3);

        expect($bundle->getBundle()[0]->getContent())->equals('Comment 1 parent');
        expect($bundle->getBundle()[0]->hasChild())->true();
        expect($bundle->getBundle()[0]->hasParent())->false();
        expect($bundle->getBundle()[0]->getChildren()[0]->getContent())->equals('Comment 2 reply to parent 1');
        expect($bundle->getBundle()[0]->getChildren()[1]->getContent())->equals('Comment 3 reply to parent 1');
    }

    public function testCreateParentWithChildWithChild()
    {
        $comments = [
            [
                'id' => 1,
                'text' => 'Comment 1 parent',
                'creator' => $this->activeUser,
                'parentComment' => null,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
            [
                'id' => 2,
                'text' => 'Comment 2 reply to parent 1',
                'creator' => $this->activeUser,
                'parentComment' => 1,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
            [
                'id' => 3,
                'text' => 'Comment 3 reply to child 2',
                'creator' => $this->activeUser,
                'parentComment' => 2,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
        ];

        $bundle = new CommentBundle($comments);

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
    }

    public function testRemoveFromBundle()
    {
        $comments = [
            [
                'id' => 1,
                'text' => 'Comment 1 parent',
                'creator' => $this->activeUser,
                'parentComment' => null,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
            [
                'id' => 2,
                'text' => 'Comment 2 reply to parent 1',
                'creator' => $this->activeUser,
                'parentComment' => 1,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
            [
                'id' => 3,
                'text' => 'Comment 3 reply to child 2',
                'creator' => $this->activeUser,
                'parentComment' => 2,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
            [
                'id' => 4,
                'text' => 'Comment 4 parent',
                'creator' => $this->activeUser,
                'parentComment' => null,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
        ];

        $bundle = new CommentBundle($comments);

        expect($bundle->removeByPrimaryKey(3))->true();
        expect($bundle->removeByPrimaryKey(8))->null();
    }

    public function testGetFromBundle()
    {
        $comments = [
            [
                'id' => 1,
                'text' => 'Comment 1 parent',
                'creator' => $this->activeUser,
                'parentComment' => null,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
            [
                'id' => 2,
                'text' => 'Comment 2 reply to parent 1',
                'creator' => $this->activeUser,
                'parentComment' => 1,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
            [
                'id' => 3,
                'text' => 'Comment 3 reply to child 2',
                'creator' => $this->activeUser,
                'parentComment' => 2,
                'createdAt' => Date::getFormatNow(),
                'status' => Comment::STATUS_ACTIVE,
            ],
        ];

        expect((new CommentBundle($comments))->findByPrimaryKey(3)->getPrimaryKey())->equals(3);
    }
}