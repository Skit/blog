<?php


namespace blog\services;

use backend\models\PostForm;
use blog\entities\category\Category;
use blog\entities\common\exceptions\MetaDataExceptions;
use blog\entities\common\MetaData;
use blog\entities\post\exceptions\PostBlogException;
use blog\entities\post\interfaces\HighlighterInterface;
use blog\entities\post\Post;
use blog\entities\post\PostBanners;
use blog\entities\user\User;

/**
 * Class PostService
 * @package blog\services
 */
final class PostService
{
    private $highlighter;

    /**
     * PostService constructor.
     * @param $highlighter
     */
    public function __construct(HighlighterInterface $highlighter)
    {
        $this->highlighter = $highlighter;
    }


    /**
     * @param PostForm $form
     * @param User $user
     * @param Category $category
     * @return Post
     * @throws PostBlogException
     */
    public function makeCreate(PostForm $form, Category $category, User $user): Post
    {
        $mediaData = PostBanners::create($form->image_url, $form->video_url);
        // TODO Выдать через статический метод
        $metaData = new MetaData($form->meta_title, $form->meta_description, $form->meta_keywords);

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

        $post->highlighting($this->highlighter);

        return $post;
    }

    /**
     * @param PostForm $form
     * @param Post $post
     * @param Category $category
     * @return Post
     * @throws PostBlogException
     */
    public function makeEdit(PostForm $form, Post $post, Category $category): Post
    {
        $mediaData = PostBanners::create($form->image_url, $form->video_url);
        // TODO Выдать через статический метод
        $metaData = new MetaData($form->meta_title, $form->meta_description, $form->meta_keywords);

        $post->edit(
                $form->title,
                $form->slug,
                $mediaData,
                $form->content,
                $form->preview,
                $metaData,
                $category,
                $form->published_at,
                $form->status
            );

        $post->highlighting($this->highlighter);

        return $post;
    }

    /**
     * @param Post $post
     * @return PostForm
     * @throws MetaDataExceptions
     * @throws PostBlogException
     */
    public function fillForm(Post $post): PostForm
    {
        $form = new PostForm();
        $form->id = $post->getPrimaryKey();
        $form->title = $post->getTitle();
        $form->slug = $post->getSlug();
        $form->preview = $post->getPreview();
        $form->content = $post->getContent();
        $form->tags = $post->getTags();
        $form->meta_title = $post->getMetaData()->getTitle();
        $form->meta_keywords = $post->getMetaData()->getKeywords();
        $form->meta_description = $post->getMetaData()->getDescription();
        $form->image_url = $post->getBanners()->getImageUrl();
        $form->video_url = $post->getBanners()->getVideoUrl();
        $form->category_id = $post->getCategory()->getPrimaryKey();
        $form->creator_id = $post->getCreator()->getPrimaryKey();
        $form->created_at = $post->getCreatedAt();
        $form->published_at = $post->getPublishedAt();
        $form->updated_at = $post->getUpdatedAt();
        $form->is_highlight = (int) $post->isHighlight();
        $form->status = $post->getStatus();
        $form->count_view = $post->getCountView();

        return $form;
    }
}