<?php

use blog\entities\post\Comment;
use blog\entities\post\exceptions\CommentException;
use blog\entities\tests\unit\comment\Base;
use Codeception\Stub;

/**
 * TODO написать тест по количеству символов
 * Class CommentCreateTest
 */
class CommentCreateTest extends Base
{
    public function testWithoutParent()
    {
        $comment = Stub::make(Comment::class, ['content' => 'Test comment', 'status' => Comment::STATUS_ACTIVE]);

        expect($comment->getContent())->equals('Test comment');
        expect($comment->hasParent())->false();
        expect($comment->isActive())->true();
    }

    public function testWithParent()
    {
        $parent = Stub::make(Comment::class, ['content' => 'Parent comment', 'status' => Comment::STATUS_ACTIVE]);
        $child = Stub::make(Comment::class, ['parentComment' => $parent, 'status' => Comment::STATUS_ACTIVE]);

        expect($child->hasParent())->true();
        expect($child->getParent()->getContent())->equals('Parent comment');
    }

    public function testWithInactiveParent()
    {
        $parent = Stub::make(Comment::class, ['content' => 'Parent comment', 'status' => Comment::STATUS_INACTIVE]);

        $this->expectExceptionMessage('Parent comment must be active');
        Comment::create( 'Reply comment', $this->activeUser, $parent, Comment::STATUS_ACTIVE);
    }

    public function testWithInactiveChild()
    {
        $parent = Stub::make(Comment::class, ['content' => 'Parent comment', 'status' => Comment::STATUS_ACTIVE]);

        $this->expectExceptionMessage('Comment must be active to set parent');
        Comment::create( 'Child comment', $this->activeUser, $parent, Comment::STATUS_INACTIVE);
    }

    public function testHasChildParent()
    {
        $parent = Comment::create('Parent comment', $this->activeUser, null, Comment::STATUS_ACTIVE);
        Comment::create('Reply comment', $this->activeUser, $parent, Comment::STATUS_ACTIVE);

        expect($parent->hasChild())->true();
    }

    public function testHasNotChild()
    {
        $parent = Comment::create( 'Parent comment', $this->activeUser, null, Comment::STATUS_ACTIVE);
        Comment::create( 'Reply comment', $this->activeUser, null, Comment::STATUS_ACTIVE);

        expect($parent->hasChild())->false();
    }

    public function testHasManyChild()
    {
        $parent = Comment::create('Parent comment', $this->activeUser, null, Comment::STATUS_ACTIVE);
        Comment::create( 'Reply comment one', $this->activeUser, $parent, Comment::STATUS_ACTIVE);
        Comment::create( 'Reply comment two', $this->activeUser, $parent, Comment::STATUS_ACTIVE);

        expect($parent->getChildren())->count(2);
    }

    public function testChildHasParentFromParent()
    {
        $parent = Comment::create('Parent comment', $this->activeUser, null, Comment::STATUS_ACTIVE);
        Comment::create('Reply comment one', $this->activeUser, $parent, Comment::STATUS_ACTIVE);

        expect($parent->getChildren()[0]->hasParent())->true();
    }

    public function testParentFromChild()
    {
        $parent = Comment::create('Parent comment', $this->activeUser, null, Comment::STATUS_ACTIVE);
        Comment::create('Reply comment one', $this->activeUser, $parent, Comment::STATUS_ACTIVE);

        expect($parent->getChildren()[0]->getParent()->getContent())->equals('Parent comment');
    }

    public function testChildParentChild()
    {
        $parent = Comment::create('Parent comment', $this->activeUser, null, Comment::STATUS_ACTIVE);
        $child = Comment::create( 'Child parent comment', $this->activeUser, $parent, Comment::STATUS_ACTIVE);
        $replyChild = Comment::create( 'Reply to child comment', $this->activeUser, $child, Comment::STATUS_ACTIVE);

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
    }

    public function testCommentWithManyUrls()
    {
        $spamText = 'http://regex101.com/ dfg fgh gh fg https://regex101.net/
sdfdfgdfg https://regex101.com/ sdfdfgfd regex101.ru/
sdfdfg regex101.com DFDFG FG D HTTP://DD.DD DFG DFG FG  YA.RY ksldnfdjfng
россия.р dfgfghfgh u.ru';

        $this->expectExceptionMessage('Your comment looks like SPAM');
        Comment::create($spamText, $this->activeUser, null, Comment::STATUS_ACTIVE);
    }

    public function testCompareComments()
    {
        $comment1 = Comment::create( 'Comment', $this->activeUser, null, Comment::STATUS_ACTIVE);
        $comment2 = Comment::create( 'Comment', $this->activeUser, null, Comment::STATUS_ACTIVE);

        expect($comment1->getHashCode() === $comment2->getHashCode())->false();
    }
}