<?php

use backend\models\TagForm;
use blog\entities\tag\Tag;
use blog\entities\tag\ArrayTagBundle;
use blog\managers\TagManager;
use blog\repositories\tag\TagRepository;
use blog\services\TagService;
use Codeception\Specify;
use Codeception\Test\Unit;

/**
 * Class TagCreateTest
 * @package blog\tests\unit\tag
 *
 * @property \Faker\Generator $faker
 * @property TagRepository $repository
 * @property TagManager $manager
 */
class TagCreateTest extends Unit
{
    use Specify;

    private $repository;
    private $manager;
    private $faker;

    public function _setUp()
    {
        $this->faker = Faker\Factory::create();
        $this->repository = new TagRepository(Yii::$app->db);
        $this->manager = new TagManager($this->repository, new TagService());

        return parent::_setUp();
    }

    public function testCreate()
    {
        $this->specify('One from form', function() {
            $form = new TagForm();
            $form->title = $this->faker->title;
            $form->slug = $this->faker->slug;
            $form->status = Tag::STATUS_ACTIVE;

            $tagPk = $this->manager->create($form);
            $tag = $this->repository->findOneById($tagPk, Tag::STATUS_ACTIVE);

            verify($tag->getTitle())->equals($form->title);
            verify($tag->getSlug())->equals($form->slug);
            verify($tag->getFrequency())->equals(0);
        });

        $this->specify('One from string form', function() {
            $bundle = new ArrayTagBundle('первый, второй, третий', Tag::STATUS_ACTIVE);

            $addedBundle = $this->manager->createByString($bundle->getFieldsString('title'));
            verify($addedBundle->getCount())->equals(3);

            $tags = $this->repository->findByNames($bundle->getFieldsString('title', '"'));

            verify($tags->getCount())->equals(3);
            verify($tags->getBundle()[0]->getFrequency())->equals(0);
            verify($tags->getBundle()[0]->getTitle())->equals('первый');
            verify($tags->getBundle()[1]->getTitle())->equals('второй');
            verify($tags->getBundle()[1]->getStatus())->equals(Tag::STATUS_ACTIVE);
            $this->assertNotEquals($tags->getBundle()[2]->getSlug(), '');
            $this->assertNotEquals($tags->getBundle()[2]->getSlug(), 'третий');
        });
    }
}