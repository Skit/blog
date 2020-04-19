<?php


namespace blog\repositories\comment;


use blog\entities\common\interfaces\ContentObjectInterface;
use blog\entities\post\Comment;
use blog\entities\relation\RelationSql;
use blog\entities\user\User;
use blog\repositories\abstracts\AbstractRepository;
use blog\repositories\exceptions\RepositoryException;
use PDO;

/**
 * Class CommentRepository
 * @package blog\repositories\comment
 */
class CommentRepository extends AbstractRepository
{
    protected $table = 'comments';
    protected $class = Comment::class;

    /**
     * @param Comment|ContentObjectInterface $comment
     * @return int
     * @throws RepositoryException
     */
    public function create(ContentObjectInterface $comment): int
    {
        $sql = 'INSERT INTO `comments` (content, parent_id, creator_id, status) VALUES (:content, :parent_id, :creator_id, :status)';

        $command = $this->dao
            ->createCommand($sql)
            ->bindValue(':content', $comment->getContent(), PDO::PARAM_STR)
            ->bindValue(':parent_id', $comment->getParent()->getPrimaryKey(), PDO::PARAM_INT)
            ->bindValue(':creator_id', $comment->getCreator()->getPrimaryKey(), PDO::PARAM_INT)
            ->bindValue(':status', $comment->getStatus(), PDO::PARAM_INT);

        return $this->checker(function () use ($command) {
            return $command->execute();})
            ->if(function ($result) {return !$result;})
            ->throw(new RepositoryException('Filed to create', 500))
            ->return(function () {
                return $this->dao->getLastInsertID();
            });
    }

    /**
     * @param int $id
     * @param int $status
     * @return Comment
     */
    public function findOneById(int $id, int $status): ContentObjectInterface
    {
        $sql = "SELECT c.id, c.content, c.parent_id, c.created_at, u.id, u.username FROM `comments` c 
                INNER JOIN `users` u ON u.`id`=c.creator_id WHERE c.`id`=:id AND c.status=:status LIMIT 1";

        $prepare = new RelationSql($sql);
        $prepare->withClass('u', User::class);

        return $this->dao->createCommandWithRelation($prepare)
            ->bindValue(':id', $id, PDO::PARAM_INT)
            ->bindValue(':status', $status, PDO::PARAM_INT)
            ->fetchOneObject($this->getClassName());
    }

    /**
     * @param ContentObjectInterface $object
     * @return int
     */
    public function update(ContentObjectInterface $object): int
    {
        // TODO: Implement update() method.
    }
}