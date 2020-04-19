<?php


namespace blog\repositories\postTag;


use blog\entities\common\PostTag;
use blog\repositories\abstracts\AbstractRepository;
use PDO;

/**
 * Class PostTagRepository
 * @package blog\repositories\postTag
 */
class PostTagRepository extends AbstractRepository
{
    protected $class = PostTag::class;

    /**
     * @param PostTag $postTag
     * @return PostTag
     */
    public function create(PostTag $postTag)
    {
        // TODO rename table to post_tag or classes to tagPost
        $sql = "INSERT INTO `tag_post` VALUES (:tag_id, :post_id)";

        $command = $this->dao
            ->createCommand($sql)
            ->bindValue(':tag_id', $postTag->getTagId(), PDO::PARAM_INT)
            ->bindValue(':post_id', $postTag->getPostId(), PDO::PARAM_INT);

        $postTag->execute($command);

        return $postTag;
    }
}