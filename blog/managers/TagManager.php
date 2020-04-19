<?php


namespace blog\managers;


use backend\models\TagForm;
use blog\entities\tag\ArrayTagBundle;
use blog\entities\tag\exceptions\TagException;
use blog\entities\tag\Tag;
use blog\entities\tag\TagBundle;
use blog\repositories\tag\TagRepository;
use blog\services\TagService;

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
     * @return int pk
     * @throws TagException
     * @throws \yii\db\Exception
     */
    public function create(TagForm $form): int
    {
        return $this->repository->create(
            Tag::create($form->title, $form->slug, $form->status)
        );
    }

    /**
     * @param string $tags
     * @return TagBundle
     * @throws TagException
     */
    public function createByString(string $tags): int
    {
        return $this->repository->createByBundle(new ArrayTagBundle($tags, Tag::STATUS_ACTIVE));
    }
}