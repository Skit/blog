<?php


namespace blog\managers;

use backend\models\CommentForm;
use blog\entities\post\Comment;
use blog\entities\post\exceptions\CommentException;
use blog\entities\user\User;
use blog\repositories\comment\CommentRepository;
use blog\repositories\exceptions\RepositoryException;
use blog\services\CommentService;

/**
 * Class CommentManager
 * @package blog\managers
 */
class CommentManager
{
    private $service;
    private $repository;

    /**
     * CommentManager constructor.
     * @param CommentService $service
     * @param CommentRepository $repository
     */
    public function __construct($repository, $service)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * @param User $creator
     * @param CommentForm $comment
     * @return Comment
     * @throws CommentException
     * @throws RepositoryException
     */
    public function create(User $creator, CommentForm $comment): Comment
    {
        $comment = Comment::create($comment->content, $creator, null, $comment->status);
        $comment->setPrimaryKey($this->repository->create($comment));

        return $comment;
    }

}