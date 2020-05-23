<?php


namespace blog\repositories\tag;


use blog\entities\common\interfaces\ContentBundleInterface;
use blog\entities\common\interfaces\ContentObjectInterface;
use blog\entities\tag\exceptions\TagException;
use blog\entities\tag\Tag;
use blog\entities\tag\TagBundle;
use blog\repositories\abstracts\AbstractRepository;
use PDO;
use yii\db\Exception;

/**
 * Class TagRepository
 * @package blog\repositories\tag
 */
class TagRepository extends AbstractRepository
{
    protected $table = 'tags';
    protected $class = Tag::class;

    /**
     * @param Tag $tag
     * @return Tag Object id
     * @throws Exception
     */
    public function create(Tag $tag): Tag
    {
        $this->dao
            ->createCommand('INSERT INTO `tags` (title, slug, status) VALUES (:title, :slug, :status)')
            ->bindValue(':title', $tag->getTitle(), PDO::PARAM_STR_CHAR)
            ->bindValue(':slug', $tag->getSlug(), PDO::PARAM_STR_CHAR)
            ->bindValue(':status', $tag->getStatus(), PDO::PARAM_INT)
            ->execute();

        // TODO сделать на чекере
        $tag->setPrimaryKey($this->dao->getLastInsertID());

        return $tag;
    }

    /**
     * Вынести в интерфейс репов
     * @param ContentBundleInterface $bundle
     * @return int
     */
    public function createFromBundle(ContentBundleInterface $bundle): int
    {
        return $this->dao
            ->createCommand()
            ->batchInsertIfNotExist($this->table, ['title', 'slug', 'status'], $bundle->getBundle(), 'status=status')
            ->execute();
    }

    /**
     * TODO вынести в итерфейс репов
     * @param string $fields
     * @return TagBundle
     * @throws TagException
     */
    public function findByNames(string $fields): ContentBundleInterface
    {
        $bundle = new TagBundle([], function () {});

        $this->dao
            ->createCommand("SELECT * FROM `tags` WHERE `title` IN ({$fields}) ORDER BY id")
            ->fetchAllObject(function(...$args) use ($bundle) {
                $bundle->append(Tag::createFull($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]));
            });

        return $bundle;
    }

    /**
     * TODO если тергать метод с манагера, то передавать анонимку можно сверху
     * @param int $id
     * @return TagBundle
     * @throws TagException
     */
    public function findAllById(int $id): TagBundle
    {
        $bundle = new TagBundle([], function () {});

        $sql = 'SELECT t.* FROM post_tag pt 
        INNER JOIN tags t on pt.tag_id = t.id 
        WHERE pt.post_id=:post_id';

        $this->dao
            ->createCommand("SELECT t.* FROM `post_tag` pt INNER JOIN tags t on pt.tag_id = t.id WHERE pt.`post_id`=:post_id ")
            ->bindValue(':post_id', $id, PDO::PARAM_INT)
            ->fetchAllObject(function(...$args) use ($bundle) {
                $bundle->append(Tag::createFull($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]));
            });

        return $bundle;
    }

    /**
     * @param Tag $tag
     * @return int
     * @throws Exception
     */
    public function delete(Tag $tag): int
    {
        return $this->dao
            ->createCommand("DELETE FROM `tags` WHERE id=:id")
            ->bindValue(':id', $tag->getPrimaryKey(), PDO::PARAM_INT)
            ->execute();
    }

    public function update(ContentObjectInterface $object): ContentObjectInterface
    {
        // TODO: Implement update() method.
    }
}