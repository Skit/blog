<?php declare(strict_types=1);

namespace blog\repositories\users;

use blog\entities\common\interfaces\ContentObjectInterface;
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
    protected $table = 'users';
    protected $class = User::class;

    /**
     * @param int $id
     * @param int $status
     * @return User
     */
    public function findOneById(int $id, int $status): ContentObjectInterface
    {
        // TODO профиля может не быть, затестить на left join
        // TODO написать тест на юзера без профиля
        $sql = "SELECT u.id, u.username, u.email, u.status FROM `users` u 
                WHERE u.`id`=:id AND u.status=:status LIMIT 1";

        $prepare = new RelationSql($sql);
        $prepare->withClass('p', Profile::class);

        return $this->dao->createCommandWithRelation($prepare)
            ->bindValue(':id', $id, PDO::PARAM_INT)
            ->bindValue(':status', $status, PDO::PARAM_INT)
            ->fetchOneObject($this->getClassName());
    }

    function create(ContentObjectInterface $object): int
    {
        // TODO: Implement create() method.
    }

    public function update(ContentObjectInterface $object): int
    {
        // TODO: Implement update() method.
    }
}