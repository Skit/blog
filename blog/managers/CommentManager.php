<?php


namespace blog\managers;

use backend\models\CommentForm;
use blog\entities\post\Comment;
use blog\entities\post\exceptions\CommentException;
use blog\entities\post\Post;
use blog\entities\user\User;
use blog\repositories\comment\CommentRepository;
use blog\repositories\exceptions\RepositoryException;
use blog\repositories\post\PostRepository;
use blog\repositories\users\UsersRepository;
use blog\services\CommentService;

/**
 * Class CommentManager
 * @package blog\managers
 */
class CommentManager
{
    private $service;
    private $repository;
    private $postRepository;
    private $userRepository;

    /**
     * CommentManager constructor.
     * @param CommentRepository $repository
     * @param UsersRepository $userRepository
     * @param PostRepository $postRepository
     * @param CommentService $service
     */
    public function __construct($repository, $userRepository, $postRepository, $service)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * TODO добавить объект post, а не получать post_id из формы
     * @param CommentForm $comment
     * @return Comment
     * @throws CommentException
     * @throws RepositoryException
     */
    public function create(CommentForm $comment): Comment
    {
        $post = $this->postRepository->findOneById($comment->post_id, Post::STATUS_ACTIVE);
        $creator = $this->userRepository->findOneById($comment->creator_id, User::STATUS_ACTIVE);
        $parent = $this->repository->findOneByPostId($comment->post_id, $comment->parent_id, Comment::STATUS_ACTIVE);

        $comment = Comment::create($comment->content, $comment->status, $parent, $post, $creator);

        $comment->setPrimaryKey($this->repository->create($comment));

        return $comment;
    }

}