<?php declare(strict_types=1);

namespace blog\entities\post;

use blog\entities\common\abstracts\BlogRecordAbstract;
use blog\entities\common\Date;
use blog\entities\post\exceptions\CommentException;
use blog\entities\post\interfaces\CommentInterface;
use blog\entities\relation\interfaces\AsRelation;
use blog\entities\relation\interfaces\HasRelation;
use blog\entities\relation\traits\AsRelationTrait;
use blog\entities\relation\traits\HasRelationTrait;
use blog\entities\user\User;
use Exception;

/**
 * TODO вынести в отдельную папку
 * Class Comment
 * @package blog\entities\post
 */
class Comment extends BlogRecordAbstract implements CommentInterface, HasRelation, AsRelation
{
    use AsRelationTrait, HasRelationTrait;

    private $parentComment;
    private $childComments;

    private $hashCode;
    private $post;

    /**
     * @param Text $text
     * @param int $status
     * @param Comment $parentComment
     * @param Post $post
     * @param User $creator
     * @return static
     * @throws CommentException
     */
    public static function create(Text $text, int $status, ?Comment $parentComment, Post $post, User $creator): self
    {
        return self::construct(0, $text, $post,
            $creator, $parentComment, (new Dates())->asNew(), $status);
    }

    /**
     * TODO аргументы идут в порядке, как в базе. Может сделать обязательные впереди, все остальные null по умолчанию
     * @param int|null $id
     * @param Text $text
     * @param Post $post
     * @param User $creator
     * @param Comment|null $parentComment
     * @param Dates $dates
     * @param int $status
     * @return static
     * @throws CommentException
     */
    public static function construct(int $id, Text $text, Post $post, User $creator, ?Comment $parentComment,
                                    Dates $dates, int $status): self
    {
        try {
            $comment = new self();

            $comment->checkActiveObject($post);
            $comment->checkActiveObject($creator);

            $comment->id = $id;
            $comment->creator_id = $creator->getPrimaryKey();

            $comment->post = $post;
            $comment->user = $creator;
            $comment->content = $text->get();

            $comment->status = $status;
            $comment->created_at = $dates->getCreatedAt();
            $comment->updated_at = $dates->getUpdatedAt();

            $comment->createHasCode();

            if ($parentComment) {
                $comment->setParent($parentComment);
                $parentComment->setChild($comment);
            }
        } catch (Exception $e) {
            throw new CommentException("Fail to create comment with: {$e->getMessage()}");
        }

        return $comment;
    }

    /**
     * @param Text $text
     * @param Comment|null $parentComment
     * @param int $status
     * @throws CommentException
     */
    public function edit(Text $text, ?Comment $parentComment, int $status): void
    {
        try {
            $this->content = $text->get();
            $this->status = $status;
            $this->updated_at = (new Dates())->asEdit()->getUpdatedAt();

            $this->createHasCode();

            if ($parentComment) {
                $this->putIn($parentComment);
            }

        } catch (Exception $e) {
            throw new CommentException("Fail to update comment with: {$e->getMessage()}");
        }
    }

    /**
     * @param Comment $parent
     * @throws CommentException
     */
    public function putIn(Comment $parent): void
    {
        $this->setParent($parent);
        $parent->setChild($this);
    }

    /**
     * @deprecated
     * TODO переделать на uuid
     */
    public function createHasCode(): void
    {
        $this->hashCode = spl_object_hash($this);
    }

    /**
     * @param Comment $child
     * @throws CommentException
     */
    public function setChild(Comment $child): void
    {
        if ($child->getHashCode() === $this->getHashCode()) {
            throw new CommentException('Child must be a different comment');
        }

        $this->childComments[] = $child;
    }

    /**
     * @param Comment $parent
     * @throws CommentException
     */
    public function setParent(Comment $parent): void
    {
        if (!$this->isActive()) {
            throw new CommentException('Comment must be active to set parent');
        }

        if (!$parent->isActive()) {
            throw new CommentException('Parent comment must be active');
        }

        if ($parent->getHashCode() === $this->getHashCode()) {
            throw new CommentException('Parent must be a different comment');
        }

        $this->parentComment = $parent;
    }

    /**
     * @return bool
     */
    public function hasParent(): bool
    {
        return $this->parentComment instanceof Comment;
    }

    /**
     * @return bool
     */
    public function hasChild(): bool
    {
        return !empty($this->childComments);
    }

    /**
     * @return string
     */
    public function getHashCode(): ?string
    {
        return $this->hashCode;
    }

    /**
     * @return Comment[]
     */
    public function getChildren(): array
    {
        return $this->childComments;
    }

    /**
     * @return Comment
     */
    public function getParent(): Comment
    {
        // TODO пустой родитель должен правильно сетиться или вернуть null
        return $this->parentComment ?? new Comment();
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->setRelationObject($name, $value);
    }
}