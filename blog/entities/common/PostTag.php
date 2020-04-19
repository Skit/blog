<?php


namespace blog\entities\common;

use blog\entities\post\Post;
use blog\entities\tag\Tag;

/**
 * Class PostTag
 * @package blog\entities\common
 */
class PostTag
{
    private $postId;
    private $tagId;

    /**
     * PostTag constructor.
     * @param Post $post
     * @param Tag $tag
     */
    public function __construct(Post $post, Tag $tag)
    {
        $this->postId = $post->getPrimaryKey();
        $this->tagId = $tag->getPrimaryKey();
    }

    /**
     * @return mixed
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * @return mixed
     */
    public function getTagId()
    {
        return $this->tagId;
    }
}