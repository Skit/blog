<?php


namespace blog\entities\common;

use blog\entities\post\Post;
use blog\entities\tag\Tag;

/**
 * Class PostTag
 * @package blog\entities\common
 */
class TagPost
{
    private $postId;
    private $tagId;

    /**
     * @param Post $post
     * @param Tag $tag
     * @return TagPost
     */
    public static function create(Post $post, Tag $tag)
    {
        $postTag = new self;
        $postTag->postId = $post->getPrimaryKey();
        $postTag->tagId = $tag->getPrimaryKey();

        return $postTag;
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