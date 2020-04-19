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

    /**
     * @param string $content
     * @param User $creator
     * @param Comment $parentComment
     * @param int $status
     * @return static
     * @throws CommentException
     */
    public static function create(string $content, User $creator, ?Comment $parentComment, int $status): self
    {
        return self::createFull(null, $content, $creator, $parentComment, (new Date())->getFormatted(), null, $status);
    }

    /**
     * @param int|null $id
     * @param string $content
     * @param User $creator
     * @param Comment|null $parentComment
     * @param string $createdAt
     * @param string|null $updatedAt
     * @param int $status
     * @return static
     * @throws CommentException
     */
    public static function createFull(?int $id, string $content, User $creator, ?Comment $parentComment,
                                      string $createdAt, ?string $updatedAt, int $status): self
    {
        try {
            $comment = new self();
            $comment->id = $id;
            $comment->checkUserToActive($creator);

            $comment->user = $creator;
            // TODO вынести валидацию из сеттера?
            $comment->setContent($content);
            $comment->status = $status;
            $comment->created_at = $createdAt;
            $comment->created_at = $updatedAt;

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
     * @param string $content
     * @param Comment|null $parentComment
     * @param int $status
     * @throws CommentException
     */
    public function edit(string $content, ?Comment $parentComment, int $status): void
    {
        try {
            // TODO вынести валидацию из сеттера?
            $this->setContent($content);
            $this->status = $status;
            $this->updated_at = (new Date())->getFormatted();

            $this->createHasCode();

            if ($parentComment) {
                $this->setParent($parentComment);
                $parentComment->setChild($this);
            }
        } catch (Exception $e) {
            throw new CommentException("Fail to update comment with:\r\n{$e->getMessage()}");
        }
    }

    /**
     * @param string $content
     * @return void
     * @throws CommentException
     */
    private function validateText(string $content): void
    {
        // TODO вынести из объекта число символов
        if (mb_strlen($content) > 300) {
            throw new CommentException("Maximum number of characters is: 300");
        }

        if (preg_match_all('~((?:https?)?(?:[\S]{3,}\.[\S]{2,}))~', $content, $match)) {
            // TODO вынести из объекта число ссылок
            if (count($match[0]) > 5) {
                throw new CommentException('Your comment looks like SPAM');
            }
        }
    }

    /**
     *
     */
    public function createHasCode(): void
    {
        $this->hashCode = spl_object_hash($this);
    }

    /**
     * @param string $content
     * @throws CommentException
     */
    public function setContent(string $content): void
    {
        $this->validateText($content);
        $this->content = $content;
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
    public function getHashCode(): string
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
        return $this->parentComment ?? new Comment();
    }

    public function __set($name, $value)
    {
        $this->setRelationObject($name, $value);
    }
}