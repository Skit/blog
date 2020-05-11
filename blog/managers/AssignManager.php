<?php declare(strict_types=1);


namespace blog\managers;


use blog\entities\common\abstracts\BlogRecordAbstract;
use blog\entities\common\abstracts\bundles\ObjectBundle;
use blog\entities\common\exceptions\BundleExteption;
use blog\entities\common\interfaces\ContentObjectInterface;
use blog\repositories\postTag\PostTagRepository;
use blog\services\AssignService;
use yii\db\Exception;

/**
 * Class AssignManager
 * @package blog\managers
 */
class AssignManager
{
    private $service;
    private $repository;

    /**
     * AssignManager constructor.
     * @param PostTagRepository $repository
     * @param AssignService $service
     */
    public function __construct(PostTagRepository $repository, AssignService $service)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * @param ObjectBundle $many
     * @param ContentObjectInterface $object
     * @return int
     * @throws BundleExteption
     * @throws Exception
     */
    public function manyTo(ObjectBundle $many, ContentObjectInterface $object)
    {
        $bundle = $this->service->makeBundle($many, $object, $this->repository->getManyField(), $this->repository->getToField());
        return $this->repository->assignFromBundle($bundle);
    }

    /**
     * @param BlogRecordAbstract $object
     * @return int
     * @throws Exception
     */
    public function revoke(BlogRecordAbstract $object)
    {
        return $this->repository->deleteAllBy($object);
    }
}