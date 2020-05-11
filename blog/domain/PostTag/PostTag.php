<?php


namespace blog\domain\PostTag;

/**
 * Class PostTag
 * @package blog\domain\PostTag
 */
class PostTag
{
    private $tag_id;
    private $post_id;

    /**
     * @param int $tag_id
     * @param int $post_id
     * @return static
     */
    public static function create(int $tag_id, int $post_id): self
    {
        $postTag = new self;
        $postTag->tag_id = $tag_id;
        $postTag->post_id = $post_id;

        return $postTag;
    }
}