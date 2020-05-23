<?php


namespace blog\managers;


use backend\models\TagForm;
use blog\entities\common\abstracts\bundles\ObjectBundle;
use blog\entities\common\interfaces\ContentBundleInterface;
use blog\entities\tag\ArrayTagBundle;
use blog\entities\tag\exceptions\TagException;
use blog\entities\tag\Tag;
use blog\entities\tag\TagBundle;
use blog\repositories\tag\TagRepository;
use blog\services\TagService;
use yii\db\Exception;

/**
 * Class TagManager
 * @package blog\managers
 */
class TagManager
{
    private $service;
    private $repository;

    /**
     * TagManager constructor.
     * @param TagRepository $repository
     * @param TagService $service
     */
    public function __construct(TagRepository $repository, TagService $service)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * @param TagForm $form
     * @return Tag pk
     * @throws Exception
     * @throws TagException
     */
    public function create(TagForm $form): Tag
    {
        return $this->repository->create(
            Tag::create($form->title, $form->slug, $form->status)
        );
    }

    /**
     * @param string $tags
     * @return ObjectBundle
     * @throws Exception
     * @throws TagException
     */
    public function createByString(string $tags): ObjectBundle
    {
        $bundle = $this->service->createBundleFromString($tags);
        // TODO всякие проверки на успешное создание
        $this->repository->createFromBundle($bundle);

        return $this->repository->findByNames(
            $bundle->getFieldsString('title', '"')
        );
    }
}