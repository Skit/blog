<?php


namespace blog\managers;


use backend\models\PostForm;
use blog\entities\category\Category;
use blog\entities\common\MetaData;
use blog\entities\post\exceptions\PostBlogException;
use blog\entities\post\PostBanners;
use blog\entities\post\Post;
use blog\entities\user\User;
use blog\repositories\category\CategoriesRepository;
use blog\repositories\exceptions\RepositoryException;
use blog\repositories\post\PostRepository;
use blog\services\PostService;

/**
 * Class PostManager
 * @package blog\managers
 */
class PostManager
{
    private $service;
    private $repository;
    private $categoryRepository;

    /**
     * CommentManager constructor.
     * @param PostRepository $repository
     * @param CategoriesRepository $categoryRepository
     * @param PostService $service
     */
    public function __construct($repository, $categoryRepository, $service)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param User $user
     * @param PostForm $form
     * @return Post
     * @throws PostBlogException
     * @throws RepositoryException
     */
    public function create(User $user, PostForm $form)
    {
        $mediaData = PostBanners::create($form->image_url, $form->video_url);
        $metaData = new MetaData($form->meta_title, $form->meta_description, $form->meta_keywords);
        $category = $this->categoryRepository->findOneById($form->category_id, Category::STATUS_ACTIVE);

        $post = Post::create(
            $form->title,
            $form->slug,
            $mediaData,
            $form->content,
            $form->preview,
            $metaData,
            $category,
            $user,
            $form->published_at,
            $form->status
        );

        $post->setPrimaryKey(
            $this->repository->create($post)
        );

        return $post;
    }
}