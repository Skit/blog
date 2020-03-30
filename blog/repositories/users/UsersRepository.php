<?php declare(strict_types=1);

namespace blog\repositories\users;

use blog\entities\relation\RelationSql;
use blog\entities\user\Profile;
use blog\entities\user\User;
use blog\repositories\abstracts\AbstractRepository;
use blog\repositories\exceptions\RepositoryException;
use PDO;

/**
 * Class UsersRepository
 * @package blog\repositories\user
 */
class UsersRepository extends AbstractRepository
{
    protected $className = User::class;

    /**
     * TODO любой или только активный?
     * @param int $id
     * @return User
     * @throws RepositoryException
     */
    public function getOneById(int $id): User
    {
        $sql = "SELECT u.id, u.username, u.email, u.status, p.avatar_url, p.bio, p.created_at FROM `users` u 
                INNER JOIN `user_profiles` p ON p.`user_id`=u.id WHERE u.`id`=:id LIMIT 1";

        $prepare = new RelationSql($sql);
        $prepare->classAlias('p', Profile::class);

        $command = $this->dao->createCommandWithRelation($prepare)
            ->bindValue(':id', $id, PDO::PARAM_INT);

        return $this->fetchOne($command);
    }
}