<?php declare(strict_types=1);


namespace blog\managers;


use backend\models\PostForm;
use blog\entities\category\Category;
use blog\entities\common\exceptions\MetaDataExceptions;
use blog\entities\post\exceptions\PostBlogException;
use blog\entities\post\Post;
use blog\entities\relation\exceptions\RelationException;
use blog\entities\tag\exceptions\TagException;
use blog\entities\user\User;
use blog\repositories\category\CategoriesRepository;
use blog\repositories\exceptions\RepositoryException;
use blog\repositories\post\PostRepository;
use blog\repositories\tag\TagRepository;
use blog\repositories\users\UsersRepository;
use blog\services\PostService;
use common\components\MTransaction;
use yii\db\Exception;

/**
 * Class PostManager
 * @package blog\managers
 */
class PostManager
{
    private $service;
    private $repository;
    private $transaction;
    private $tagManager;
    private $tagRepository;
    private $userRepository;
    private $assignManager;
    private $categoryRepository;

    /**
     * CommentManager constructor.
     * @param PostRepository $repository
     * @param CategoriesRepository $categoryRepository
     * @param UsersRepository $userRepository
     * @param TagRepository $tagRepository
     * @param AssignManager $assignManager
     * @param TagManager $tagManager
     * @param MTransaction $transaction
     * @param PostService $service
     */
    public function __construct($repository, $categoryRepository, $userRepository, $tagRepository,
                                $assignManager, $tagManager, $transaction, $service)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->transaction = $transaction;
        $this->tagManager = $tagManager;
        $this->tagRepository = $tagRepository;
        $this->userRepository = $userRepository;
        $this->assignManager = $assignManager;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param PostForm $form
     * @return Post
     * @throws PostBlogException
     * @throws RelationException
     * @throws RepositoryException
     * @throws TagException
     */
    public function create(PostForm $form)
    {
        $user = $this->userRepository->findOneById($form->creator_id, User::STATUS_ACTIVE);
        $category = $this->categoryRepository->findOneById($form->category_id, Category::STATUS_ACTIVE);
        $post = $this->service->makeCreate($form, $category, $user);

        $this->transaction->run(function () use ($post, $form) {
            $post = $this->repository->create($post);
            $tagsBundle = $this->tagManager->createByString($form->tags);
            $this->assignManager->manyTo($tagsBundle, $post);
        });

        $post->setTags($this->tagRepository->findAllById($post->getPrimaryKey()));

        return $post;
    }

    /**
     * TODO в мэнэджере поиск только по объекту Post
     * @param PostForm $post
     * @return Post
     * @throws PostBlogException
     * @throws RelationException
     * @throws RepositoryException
     * @throws TagException
     */
    public function find(PostForm $post): Post
    {
        $post = $this->repository->findOneById($post->id, $post->status);
        $post->setTags($this->tagRepository->findAllById($post->getPrimaryKey()));

        return $post;
    }

    /**
     * @param Post $post
     * @return PostForm
     * @throws PostBlogException
     * @throws MetaDataExceptions
     */
    public function getForm(Post $post)
    {
        return $this->service->fillForm($post);
    }

    /**
     * @param PostForm $form
     * @param Post $post
     * @return Post
     * @throws PostBlogException
     * @throws RelationException
     * @throws RepositoryException
     */
    public function update(PostForm $form, Post $post): Post
    {
        $category = $this->categoryRepository->findOneById($form->category_id, Category::STATUS_ACTIVE);
        $post = $this->service->makeEdit($form, $post, $category);

        $this->transaction->run(function () use ($post, $form) {
            $this->repository->update($post);
            $this->assignManager->revoke($post);
            $tagsBundle = $this->tagManager->createByString($form->tags);
            $this->assignManager->manyTo($tagsBundle, $post);
        });

        return $post;
    }

    /**
     * @param Post $post
     * @return int
     * @throws Exception
     */
    public function delete(Post $post): int
    {
       return $this->repository->deleteById($post->getPrimaryKey());
    }
}