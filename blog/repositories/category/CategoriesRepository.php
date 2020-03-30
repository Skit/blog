<?php declare(strict_types=1);

namespace blog\repositories\category;

use blog\entities\category\Category;
use blog\entities\category\exceptions\CategoryException;
use blog\entities\common\exceptions\MetaDataExceptions;
use blog\entities\relation\RelationSql;
use blog\entities\user\Profile;
use blog\entities\user\User;
use blog\repositories\abstracts\AbstractRepository;
use blog\repositories\exceptions\RepositoryException;
use PDO;

/**
 * Class CategoriesRepository
 *
 * TODO: SET sql_mode='STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE';
 * @package blog\repositories\category
 */
final class CategoriesRepository extends AbstractRepository
{
    protected $className = Category::class;

    /**
     * @param Category $category
     * @return Category
     * @throws MetaDataExceptions
     * @throws RepositoryException
     * @throws CategoryException
     */
    public function create(Category $category): Category
    {
        $sql = "INSERT INTO `categories` VALUES (NULL, :title, :slug, :description, :meta_data, :creator_id, :created_at, :updated_at, :status)";

        $command = $this->dao
            ->createCommand($sql)
            ->bindValue(':title', $category->getTitle(), PDO::PARAM_STR_CHAR)
            ->bindValue(':slug', $category->getSlug(), PDO::PARAM_STR_CHAR)
            ->bindValue(':description', $category->getContent(), PDO::PARAM_STR)
            ->bindValue(':meta_data', $category->getMetaData(), PDO::PARAM_STR)
            ->bindValue(':creator_id', $category->getCreator()->getPrimaryKey(), PDO::PARAM_INT)
            ->bindValue(':created_at', $category->getCreatedAt(), PDO::PARAM_STR_CHAR)
            ->bindValue(':updated_at', null, PDO::PARAM_NULL)
            ->bindValue(':status', $category->getStatus(), PDO::PARAM_INT);

        $this->execute($command);

        $category->setPrimaryKey((int) $this->dao->getLastInsertID());
        return $category;
    }

    /**
     * @param Category $category
     * @return int
     * @throws RepositoryException
     * @throws MetaDataExceptions
     */
    public function update(Category $category): int
    {
        $sql = "UPDATE `categories` SET 
                    `title`=:title,
                    `slug`=:slug, 
                    `meta_data`=:meta_data, 
                    `description`=:description,
                    `updated_at`=:updated_at,
                    `status`=:status
                    WHERE id=:id LIMIT 1";

        $command = $this->dao->createCommand($sql)
            ->bindValue(':id', $category->getPrimaryKey(), PDO::PARAM_STR_CHAR)
            ->bindValue(':title', $category->getTitle(), PDO::PARAM_STR_CHAR)
            ->bindValue(':slug', $category->getSlug(), PDO::PARAM_STR_CHAR)
            ->bindValue(':description', $category->getContent(), PDO::PARAM_STR)
            ->bindValue(':meta_data', $category->getMetaData(), PDO::PARAM_STR)
            ->bindValue(':updated_at', $category->getUpdatedAt(), PDO::PARAM_STR_CHAR)
            ->bindValue(':status', $category->getStatus(), PDO::PARAM_INT);

        return $this->execute($command);
    }

    /**
     * @param int $id
     * @return Category
     * @throws RepositoryException
     */
    public function getOneActiveById(int $id): Category
    {
        $sql = "SELECT c.*, u.id, u.username, u.email, u.status FROM `categories` c 
        INNER JOIN `users` u ON c.creator_id=u.id WHERE c.`id`=:id AND c.`status`=:status LIMIT 1";

        $prepare = new RelationSql($sql);
        $prepare->classAlias('u', User::class);

        $command = $this->dao
            ->createCommandWithRelation($prepare)
            ->bindValue(':id', $id, PDO::PARAM_INT)
            ->bindValue(':status', Category::STATUS_ACTIVE, PDO::PARAM_INT);

        return $this->fetchOne($command);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws RepositoryException
     */
    public function getOneById(int $id)
    {
        $sql = "SELECT c.*, u.id, u.username, u.email, u.status, p.`bio`, p.`avatar_url`
                FROM `categories` c 
                INNER JOIN `users` u ON c.creator_id=u.id 
                LEFT JOIN `user_profiles` p ON p.`user_id`=u.`id`
                WHERE c.`id`=:id LIMIT 1";

        $prepare = new RelationSql($sql);
        $prepare->classAlias('u', User::class)->has('p', Profile::class);

        $command = $this->dao
            ->createCommandWithRelation($prepare)
            ->bindValue(':id', $id, PDO::PARAM_INT);

        return $this->fetchOne($command);
    }
}