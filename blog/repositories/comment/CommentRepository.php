<?php


namespace blog\repositories\comment;


use blog\entities\common\interfaces\ContentObjectInterface;
use blog\entities\post\Comment;
use blog\entities\post\CommentBundle;
use blog\entities\post\Post;
use blog\entities\relation\exceptions\RelationException;
use blog\entities\relation\RelationSql;
use blog\entities\user\User;
use blog\repositories\abstracts\AbstractRepository;
use blog\repositories\exceptions\RepositoryException;
use Exception;
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
        $sql = 'INSERT INTO `comments` (content, post_id, parent_id, creator_id, status) 
                VALUES (:content, :post_id, :parent_id, :creator_id, :status)';

        $command = $this->dao
            ->createCommand($sql)
            ->bindValue(':content', $comment->getContent(), PDO::PARAM_STR)
            ->bindValue(':post_id', $comment->getPost()->getPrimaryKey(), PDO::PARAM_INT)
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
        // TODO докинуть профиль
        $prepare->withClass('u', User::class);

        return $this->dao->createCommandWithRelation($prepare)
            ->bindValue(':id', $id, PDO::PARAM_INT)
            ->bindValue(':status', $status, PDO::PARAM_INT)
            ->fetchOneObject($this->getClassName());
    }

    /**
     * TODO сделать поиск по объекту поста
     * @param int $postId
     * @param int $id
     * @param int $status
     * @return ContentObjectInterface|Comment|null
     * @throws RelationException
     * @throws RepositoryException
     */
    public function findOneByPostId(int $postId, int $id, int $status): ?ContentObjectInterface
    {
        $sql = "SELECT c.id, c.content, c.parent_id, c.created_at, c.status, u.id, u.username FROM `comments` c 
                INNER JOIN `users` u ON u.`id`=c.creator_id 
                WHERE c.`id`=:id AND c.`post_id`=:post_id AND c.status=:status LIMIT 1";

        $prepare = new RelationSql($sql);
        // TODO докинуть профиль
        $prepare->withClass('u', User::class);

        // TODO если не найдено, нужно вернуть null
        $result = $this->dao->createCommandWithRelation($prepare)
            ->bindValue(':id', $id, PDO::PARAM_INT)
            ->bindValue(':post_id', $postId, PDO::PARAM_INT)
            ->bindValue(':status', $status, PDO::PARAM_INT)
            ->fetchOneObject($this->getClassName());

        return $result ?: null;
    }

    /**
     * @param Post $post
     * @param int $status
     * @return CommentBundle
     * @throws RelationException
     * @throws RepositoryException
     */
    public function findAllByPost(Post $post, int $status): CommentBundle
    {
        $sql = "SELECT c.id, c.content, c.created_at, c.updated_at, c.status, u.id, u.username, u.role, u.status, cp.id, cp.status, cpu.id, cpu.username, cpu.role, cpu.status FROM `comments` c 
                LEFT JOIN users u ON c.creator_id = u.id
                LEFT JOIN comments cp ON c.parent_id=cp.id
                LEFT JOIN users cpu ON cp.creator_id=cpu.id
                WHERE c.`post_id`=:post_id AND c.status=:status ORDER BY c.id, c.parent_id";

        $prepare = new RelationSql($sql);
        $prepare->withClass('u', User::class)
                ->withClass('cp', Comment::class);

        try {
            $bundle = new CommentBundle();
            // TODO возвращает пустой массив с null по количеству записей. подумать над проверкой результата
            $this->dao->createCommandWithRelation($prepare)
                ->bindValue(':post_id', $post->getPrimaryKey(), PDO::PARAM_INT)
                ->bindValue(':status', $status, PDO::PARAM_INT)
                ->fetchAllObject(function(...$args) use ($bundle, $post) {
                        if ($args[9]) {
                            $parentCreator = User::construct($args[11], $args[12], $args[13], null, null, null, null, $args[14], null, null, null, null);
                            $parentComment = Comment::construct($args[9], '', $post, $parentCreator, null, null, null, $args[10]);
                        }
                        $creator = User::construct($args[5], $args[6], $args[7], null, null, null, null, $args[8], null, null, null, null);
                        $bundle->append(Comment::construct($args[0], $args[1], $post, $creator, $parentComment ?? null, $args[2], $args[3], $args[4]));
                    });
        } catch (Exception $e) {
            // TODO Переписать сам экзепшн, чтобы он выдавал нужный нормальный мессэдж.
            throw new RepositoryException("Stack: {$e->getMessage()}\n{$e->getPrevious()->getFile()}\n{$e->getPrevious()->getMessage()}");
        }

       return $bundle;
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