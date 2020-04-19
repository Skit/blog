<?php


namespace blog\repositories\post;


use blog\entities\category\Category;
use blog\entities\common\interfaces\ContentObjectInterface;
use blog\entities\post\PostBanners;
use blog\entities\post\Post;
use blog\entities\relation\exceptions\RelationException;
use blog\entities\relation\RelationSql;
use blog\entities\user\Profile;
use blog\entities\user\User;
use blog\repositories\abstracts\AbstractRepository;
use blog\repositories\exceptions\RepositoryException;
use PDO;

/**
 * Class PostRepository
 * @package blog\repositories\post
 */
class PostRepository extends AbstractRepository
{
    protected $table = 'posts';
    protected $class = Post::class;

    /**
     * @param Post|ContentObjectInterface $post
     * @return int
     * @throws RepositoryException
     */
    public function create(ContentObjectInterface $post): int
    {
        $sql = "INSERT INTO `posts` VALUES 
                           (NULL, :title, :slug, :preview, :content, :meta_data, :post_banners, :category_id, :creator_id, :created_at, :published_at, NULL, :is_highlight, :status, :count_view)";

        $command = $this->dao
            ->createCommand($sql)
            ->bindValue(':title', $post->getTitle(), PDO::PARAM_STR_CHAR)
            ->bindValue(':slug', $post->getSlug(), PDO::PARAM_STR_CHAR)
            ->bindValue(':content', $post->getContent(), PDO::PARAM_STR)
            ->bindValue(':preview', $post->getPreview(), PDO::PARAM_STR)
            ->bindValue(':meta_data', $post->getMetaData(), PDO::PARAM_STR)
            // TODO согласовать имена post_banners и getMediaUrls
            ->bindValue(':post_banners', $post->getBanners(), PDO::PARAM_STR)
            ->bindValue(':category_id', $post->getCategory()->getPrimaryKey(), PDO::PARAM_INT)
            ->bindValue(':creator_id', $post->getCreator()->getPrimaryKey(), PDO::PARAM_INT)
            ->bindValue(':published_at', $post->getPublishedAt(), PDO::PARAM_STR_CHAR)
            ->bindValue(':created_at', $post->getCreatedAt(), PDO::PARAM_STR_CHAR)
            ->bindValue(':is_highlight', 0, PDO::PARAM_INT)
            ->bindValue(':status', $post->getStatus(), PDO::PARAM_INT)
            ->bindValue(':count_view', $post->getCountView(), PDO::PARAM_INT);

        return $this->checker(function () use ($command) {
            return $command->execute();
        })
        ->if(function ($result) {
            return !$result;
        })
        ->throw(new RepositoryException('Filed to create', 500))
        ->return(function () {
            return $this->dao->getLastInsertID();
        });
    }

    /**
     * @param int $id
     * @param int $status
     * @return Post
     * @throws RepositoryException
     * @throws RelationException
     */
    public function findOneById(int $id, int $status): ContentObjectInterface
    {
        $sql = "SELECT p.*, cat.title, cat.content, cat.created_at, up.bio, up.avatar_url, u.id, u.username FROM `posts` p 
                INNER JOIN `categories` cat ON p.`category_id` = cat.`id`
                INNER JOIN `users` u ON u.`id`=p.`creator_id`
                LEFT JOIN `user_profiles` up on u.`id`=up.`user_id`
                WHERE p.`id`=:id AND p.`status`=:status LIMIT 1";

        $prepare = new RelationSql($sql);
        $prepare->withClass('u', User::class)->thatHas('up', Profile::class)
                ->withClass('cat', Category::class);

        return $this->dao->createCommandWithRelation($prepare)
            ->bindValue(':id', $id, PDO::PARAM_INT)
            ->bindValue(':status', $status, PDO::PARAM_INT)
            ->fetchOneObject($this->getClassName());
    }

    public function update(ContentObjectInterface $object): int
    {
        // TODO: Implement update() method.
    }
}