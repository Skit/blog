<?php


namespace blog\repositories\post;


use blog\entities\category\Category;
use blog\entities\common\exceptions\MetaDataExceptions;
use blog\entities\common\interfaces\ContentObjectInterface;
use blog\entities\post\exceptions\PostBlogException;
use blog\entities\post\Post;
use blog\entities\relation\exceptions\RelationException;
use blog\entities\relation\RelationSql;
use blog\entities\user\Profile;
use blog\entities\user\User;
use blog\repositories\abstracts\AbstractRepository;
use blog\repositories\exceptions\RepositoryException;
use PDO;

/**
 * TODO кинуть экзепшны если не найдена или не обновлена запись
 * Class PostRepository
 * @package blog\repositories\post
 */
class PostRepository extends AbstractRepository
{
    protected $table = 'posts';
    protected $class = Post::class;

    /**
     * @param Post $post
     * @return Post
     * @throws MetaDataExceptions
     * @throws PostBlogException
     * @throws RepositoryException
     */
    public function create(Post $post): ContentObjectInterface
    {
        $sql = "INSERT INTO `posts` VALUES 
                           (NULL, :uuid, :title, :slug, :preview, :content, :highlighted_content, :zip_content, :meta_data, :post_banners, :category_id, :creator_id, :created_at, :published_at, NULL, :is_highlight, :status, :count_view)";

        $command = $this->dao
            ->createCommand($sql)
            ->bindValue(':uuid', $post->getUuid(), PDO::PARAM_STR_CHAR)
            ->bindValue(':title', $post->getTitle(), PDO::PARAM_STR_CHAR)
            ->bindValue(':slug', $post->getSlug(), PDO::PARAM_STR_CHAR)
            ->bindValue(':content', $post->getContent(), PDO::PARAM_STR)
            ->bindValue(':highlighted_content', $post->getHighlightContent(), PDO::PARAM_STR)
            ->bindValue(':zip_content', $post->getZipContent(), PDO::PARAM_STR)
            ->bindValue(':preview', $post->getPreview(), PDO::PARAM_STR)
            ->bindValue(':meta_data', $post->getMetaData(), PDO::PARAM_STR)
            // TODO согласовать имена post_banners и getMediaUrls
            ->bindValue(':post_banners', $post->getBanners(), PDO::PARAM_STR)
            ->bindValue(':category_id', $post->getCategory()->getPrimaryKey(), PDO::PARAM_INT)
            ->bindValue(':creator_id', $post->getCreator()->getPrimaryKey(), PDO::PARAM_INT)
            ->bindValue(':published_at', $post->getPublishedAt(), PDO::PARAM_STR_CHAR)
            ->bindValue(':created_at', $post->getCreatedAt(), PDO::PARAM_STR_CHAR)
            ->bindValue(':is_highlight', (int) $post->isHighlight(), PDO::PARAM_INT)
            ->bindValue(':status', $post->getStatus(), PDO::PARAM_INT)
            ->bindValue(':count_view', $post->getCountView(), PDO::PARAM_INT);

        $pk = $this->checker(function () use ($command) {
                return $command->execute();})
            ->if(function ($result) {
                return !$result;})
            ->throw(new RepositoryException('Filed to create', 500))
            ->return(function () {
                return $this->dao->getLastInsertID();
             });

        $post->setPrimaryKey($pk);

        return $post;
    }

    /**
     * @param int $id
     * @param int $status
     * @return Post
     * @throws RelationException
     * @throws RepositoryException
     */
    public function findOneById(int $id, int $status): ?ContentObjectInterface
    {
        $sql = "SELECT p.*, cat.id, cat.title, cat.content, cat.created_at, up.bio, up.avatar_url, u.id, u.username FROM `posts` p 
                INNER JOIN `categories` cat ON p.`category_id` = cat.`id`
                INNER JOIN `users` u ON u.`id`=p.`creator_id`
                LEFT JOIN `user_profiles` up on u.`id`=up.`user_id`
                WHERE p.`id`=:id AND p.`status`=:status LIMIT 1";

        $prepare = new RelationSql($sql);
        $prepare->withClass('u', User::class)->thatHas('up', Profile::class)
                ->withClass('cat', Category::class);

        /* @var Post $post */
        $post =  $this->dao->createCommandWithRelation($prepare)
            ->bindValue(':id', $id, PDO::PARAM_INT)
            ->bindValue(':status', $status, PDO::PARAM_INT)
            ->fetchOneObject($this->getClassName());

        return $post ?: null;
    }

    /**
     * TODO оставить поиск только по uuid
     * TODO здесь не нужно джойнить всё подряд, сделать включение связей по необходимости
     * @param string $uuid
     * @param int $status
     * @return Post|null
     * @throws RelationException
     * @throws RepositoryException
     */
    public function findOneByUuid(string $uuid, int $status): ?ContentObjectInterface
    {
        $sql = "SELECT p.*, cat.id, cat.title, cat.content, cat.created_at, up.bio, up.avatar_url, u.id, u.username FROM `posts` p 
                INNER JOIN `categories` cat ON p.`category_id` = cat.`id`
                INNER JOIN `users` u ON u.`id`=p.`creator_id`
                LEFT JOIN `user_profiles` up on u.`id`=up.`user_id`
                WHERE p.`uuid`=:uuid AND p.`status`=:status LIMIT 1";

        $prepare = new RelationSql($sql);
        $prepare->withClass('u', User::class)->thatHas('up', Profile::class)
            ->withClass('cat', Category::class);

        /* @var Post $post */
        $post =  $this->dao->createCommandWithRelation($prepare)
            ->bindValue(':uuid', $uuid, PDO::PARAM_STR)
            ->bindValue(':status', $status, PDO::PARAM_INT)
            ->fetchOneObject($this->getClassName());

        return $post ?: null;
    }

    /**
     * @param int $id
     * @return Post|null
     * @throws RelationException
     * @throws RepositoryException
     */
    public function findAnyById(int $id): ?Post
    {
        $sql = "SELECT p.*, cat.id, cat.title, cat.content, cat.created_at, up.bio, up.avatar_url, u.id, u.username FROM `posts` p 
                INNER JOIN `categories` cat ON p.`category_id` = cat.`id`
                INNER JOIN `users` u ON u.`id`=p.`creator_id`
                LEFT JOIN `user_profiles` up on u.`id`=up.`user_id`
                WHERE p.`id`=:id LIMIT 1";

        $prepare = new RelationSql($sql);
        $prepare->withClass('u', User::class)->thatHas('up', Profile::class)
            ->withClass('cat', Category::class);

        /* @var Post $post */
        $post =  $this->dao->createCommandWithRelation($prepare)
            ->bindValue(':id', $id, PDO::PARAM_INT)
            ->fetchOneObject($this->getClassName());

        return $post ?: null;
    }

    /**
     * TODO сделать тесты и вынести метод в базловый класс
     * @param Post $post
     * @param array $attributes
     * @return int
     */
    public function updateAttribute(Post $post, array $attributes)
    {
        $sql = 'UPDATE `posts` SET';
        foreach ($attributes as $key => $value) {
            $chromakey = ":{$key}";
            $sql .= " {$key}=$chromakey,";
            $attributes += [$chromakey => $value];
            unset($attributes[$key]);
        }

        $command = $this->dao
            ->createCommand(rtrim($sql, ',') . ' WHERE id=:id')
            ->bindValues(array_merge($attributes, [
                ':id' => $post->getPrimaryKey()
            ]));

        return $command->execute();
    }

    /**
     * @param Post $post
     * @return int
     * @throws PostBlogException
     * @throws MetaDataExceptions
     */
    public function update(Post $post): int
    {
        $sql = 'UPDATE `posts` SET 
                    `title`=:title,
                    `slug`=:slug, 
                    `preview`=:preview,
                    `content`=:content,
                    `highlighted_content`=:highlighted_content, 
                    `zip_content`=:zip_content,
                    `meta_data`=:meta_data, 
                    `post_banners`=:post_banners, 
                    `category_id`=:category_id, 
                    `creator_id`=:creator_id, 
                    `published_at`=:published_at,
                    `updated_at`=:updated_at,
                    `is_highlight`=:is_highlight,
                    `status`=:status
                    WHERE id=:id LIMIT 1';

        $command = $this->dao
            ->createCommand($sql)
            ->bindValue(':id', $post->getPrimaryKey(), PDO::PARAM_INT)
            ->bindValue(':title', $post->getTitle(), PDO::PARAM_STR_CHAR)
            ->bindValue(':slug', $post->getSlug(), PDO::PARAM_STR_CHAR)
            ->bindValue(':preview', $post->getPreview(), PDO::PARAM_STR)
            ->bindValue(':content', $post->isHighlight() ? null : $post->getContent(), PDO::PARAM_STR)
            ->bindValue(':highlighted_content', $post->getHighlightContent(), PDO::PARAM_STR)
            ->bindValue(':zip_content', $post->getZipContent(), PDO::PARAM_STR)
            ->bindValue(':meta_data', $post->getMetaData(), PDO::PARAM_STR)
            // TODO согласовать имена post_banners и getMediaUrls
            ->bindValue(':post_banners', $post->getBanners(), PDO::PARAM_STR)
            ->bindValue(':category_id', $post->getCategory()->getPrimaryKey(), PDO::PARAM_INT)
            ->bindValue(':creator_id', $post->getCreator()->getPrimaryKey(), PDO::PARAM_INT)
            ->bindValue(':published_at', $post->getPublishedAt(), PDO::PARAM_STR_CHAR)
            ->bindValue(':updated_at', $post->getUpdatedAt(), PDO::PARAM_STR_CHAR)
            ->bindValue(':is_highlight', (int) $post->isHighlight(), PDO::PARAM_INT)
            ->bindValue(':status', $post->getStatus(), PDO::PARAM_INT);

        return $command->execute();
    }

    /**
     * @param Post $post
     * @param int $status
     * @return int
     */
    public function changeStatus(Post $post, int $status)
    {
        return $this->dao
            ->createCommand('UPDATE `posts` SET `status`=:status WHERE id=:id')
            ->bindValue(':id', $post->getPrimaryKey(), PDO::PARAM_INT)
            ->bindValue(':status', $status, PDO::PARAM_INT)
            ->execute();
    }


    /**
     * @param int $postId
     * @return int
     */
    public function deleteById(int $postId)
    {
        return $this->dao
            ->createCommand("DELETE FROM posts WHERE id=:id")
            ->bindValue(':id', $postId, PDO::PARAM_INT)
            ->execute();
    }
}