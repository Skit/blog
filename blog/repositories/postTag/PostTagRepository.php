<?php


namespace blog\repositories\postTag;


use blog\entities\common\abstracts\BlogRecordAbstract;
use blog\entities\common\interfaces\ContentBundleInterface;
use blog\entities\common\TagPost;
use blog\entities\post\Post;
use blog\entities\tag\exceptions\TagException;
use blog\entities\tag\Tag;
use blog\entities\tag\TagBundle;
use blog\repositories\abstracts\AbstractAssignRepository;
use blog\repositories\exceptions\RepositoryException;
use PDO;
use yii\db\Exception;

/**
 * TODO переделать абстракный репозиторий, чтобы он не обзывал имплементить круд интрефейс
 * Class PostTagRepository
 * @package blog\repositories\postTag
 */
final class PostTagRepository extends AbstractAssignRepository
{
    protected $class = TagPost::class;
    protected $table = 'post_tag';
    protected $toField = 'post_id';
    protected $manyField = 'tag_id';

    /**
     * @param TagPost $postTag
     * @return int
     * @throws RepositoryException
     */
    public function assign(TagPost $postTag): int
    {
        $command = $this->dao
            ->createCommand("INSERT INTO `{$this->table}` VALUES (:tag_id, :post_id)")
            ->bindValue(":{$this->getManyField()}", $postTag->getTagId(), PDO::PARAM_INT)
            ->bindValue(":{$this->getToField()}", $postTag->getPostId(), PDO::PARAM_INT);

        return $this->checker(function () use ($command) {
            return $command->execute();})
            ->if(function ($result) {
                return !$result;})
            // TODO сделать свой экзепшн
            ->throw(new RepositoryException('Filed to create', 500))
            ->return(function () {
                return $this->dao->getLastInsertID();
            });
    }

    /**
     * @param BlogRecordAbstract $record
     * @return int
     * @throws Exception
     */
    public function deleteAllBy(BlogRecordAbstract $record)
    {
        return $this->dao
            ->createCommand()
            ->delete($this->table, "{$this->getToField()}=:{$this->getToField()}",
                [":{$this->getToField()}" => $record->getPrimaryKey()])
            ->execute();
    }

    /**
     * @param ContentBundleInterface $bundle
     * @return int
     * @throws Exception
     */
    public function assignFromBundle(ContentBundleInterface $bundle): int
    {
        return $this->dao
            ->createCommand()
            ->batchInsert($this->table, [$this->getToField(), $this->getManyField()], $bundle->getBundle())
            ->execute();
    }
}