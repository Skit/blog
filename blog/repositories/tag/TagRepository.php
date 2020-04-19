<?php


namespace blog\repositories\tag;


use blog\entities\common\interfaces\ContentBundleInterface;
use blog\entities\common\interfaces\ContentObjectInterface;
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
     * @return int Object id
     * @throws Exception
     */
    public function create($tag): int
    {
        $result = $this->dao
            ->createCommand('INSERT INTO `tags` (title, slug, status) VALUES (:title, :slug, :status)')
            ->bindValue(':title', $tag->getTitle(), PDO::PARAM_STR_CHAR)
            ->bindValue(':slug', $tag->getSlug(), PDO::PARAM_STR_CHAR)
            ->bindValue(':status', $tag->getStatus(), PDO::PARAM_INT)
            ->execute();

        return $result ? $this->dao->getLastInsertID() : 0;
    }

    /**
     * Вынести в интерфейс репов
     * @param ContentBundleInterface $bundle
     * @return int Rows affected
     * @throws Exception
     */
    public function createByBundle(ContentBundleInterface $bundle): int
    {
        return $this->dao
            ->createCommand()
            ->batchInsert($this->table, ['title', 'slug', 'status'], $bundle->getBundle())
            ->execute();
    }

    /**
     * TODO вынести в итерфейс репов
     * @param string $fields
     * @return TagBundle
     */
    public function findByNames(string $fields): ContentBundleInterface
    {
        $bundle = new TagBundle();
        $this->dao
            ->createCommand("SELECT * FROM `tags` WHERE `title` IN ({$fields})")
            ->fetchAllObject(function(...$args) use ($bundle) {
                $bundle->append(Tag::createFull($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]));
            });

        return $bundle;
    }

    public function update(ContentObjectInterface $object): int
    {
        // TODO: Implement update() method.
    }
}