<?php declare(strict_types=1);

namespace blog\repositories\category;

use blog\entities\category\Category;
use blog\entities\common\interfaces\ContentObjectInterface;
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
 * TODO: Переименовать в единственное число
 * @package blog\repositories\category
 */
final class CategoriesRepository extends AbstractRepository
{
    protected $class = Category::class;

    /**
     * @param ContentObjectInterface $category
     * @return int
     * @throws RepositoryException
     */
    public function create(ContentObjectInterface $category): ContentObjectInterface
    {
        $sql = 'INSERT INTO `categories` 
VALUES (NULL, :title, :slug, :content, :meta_data, :creator_id, :created_at, :updated_at, :status)';

        $command =  $this->dao
            ->createCommand($sql)
            ->bindValue(':title', $category->getTitle(), PDO::PARAM_STR_CHAR)
            ->bindValue(':slug', $category->getSlug(), PDO::PARAM_STR_CHAR)
            ->bindValue(':content', $category->getContent(), PDO::PARAM_STR)
            ->bindValue(':meta_data', $category->getMetaData(), PDO::PARAM_STR)
            ->bindValue(':creator_id', $category->getCreator()->getPrimaryKey(), PDO::PARAM_INT)
            ->bindValue(':created_at', $category->getCreatedAt(), PDO::PARAM_STR_CHAR)
            ->bindValue(':updated_at', null, PDO::PARAM_NULL)
            ->bindValue(':status', $category->getStatus(), PDO::PARAM_INT);

        $pk = $this->checker(function () use ($command) {
            return $command->execute();
        })
            ->if(function ($result) {
                return !$result;
            })
            ->throw(new RepositoryException('Filed to create', 500))
            ->return(function () {
                return (int) $this->dao->getLastInsertID();
            });

        $category->setPrimaryKey($pk);

        return $category;
    }

    /**
     * @param ContentObjectInterface $category
     * @return int
     * @throws RepositoryException
     */
    public function update(ContentObjectInterface $category): ContentObjectInterface
    {
        $sql = 'UPDATE `categories` SET 
                    `title`=:title,
                    `slug`=:slug, 
                    `meta_data`=:meta_data, 
                    `content`=:content,
                    `updated_at`=:updated_at,
                    `status`=:status
                    WHERE id=:id LIMIT 1';

        $command = $this->dao->createCommand($sql)
            ->bindValue(':id', $category->getPrimaryKey(), PDO::PARAM_STR_CHAR)
            ->bindValue(':title', $category->getTitle(), PDO::PARAM_STR_CHAR)
            ->bindValue(':slug', $category->getSlug(), PDO::PARAM_STR_CHAR)
            ->bindValue(':content', $category->getContent(), PDO::PARAM_STR)
            ->bindValue(':meta_data', $category->getMetaData(), PDO::PARAM_STR)
            ->bindValue(':updated_at', $category->getUpdatedAt(), PDO::PARAM_STR_CHAR)
            ->bindValue(':status', $category->getStatus(), PDO::PARAM_INT);

        $pk = $this->checker(function () use ($command) {
            return $command->execute();
        })
            ->if(function ($result) {
                return !$result;
            })
            ->throw(new RepositoryException('Filed to update', 500))
            ->return(function () {
                return (int) $this->dao->getLastInsertID();
            });

        $category->setPrimaryKey($pk);

        return $category;
    }

    /***
     * @param int $id
     * @param int $status
     * @return ContentObjectInterface
     * @throws RepositoryException
     */
    public function findOneById(int $id, int $status): ContentObjectInterface
    {
        $sql = 'SELECT c.*, u.id, u.username, u.email, u.status, p.`bio`, p.`avatar_url`
                FROM `categories` c 
                INNER JOIN `users` u ON c.creator_id=u.id 
                LEFT JOIN `user_profiles` p ON p.`user_id`=u.`id`
                WHERE c.`status`=:status AND c.`id`=:id LIMIT 1';

        $prepare = new RelationSql($sql);
        $prepare->withClass('u', User::class)->thatHas('p', Profile::class);

        $command = $this->dao
            ->createCommandWithRelation($prepare)
            ->bindValue(':id', $id, PDO::PARAM_INT)
            ->bindValue(':status', $status, PDO::PARAM_INT);

        return $this->checker(function () use ($command) {
            return $command->fetchOneObject($this->getClassName());
        })
        ->if(function ($result) {
            return !$result;
        })
        ->throw(new RepositoryException('Record does not found', 404))
        ->return(function ($record) {
            return $record;
        });
    }
}