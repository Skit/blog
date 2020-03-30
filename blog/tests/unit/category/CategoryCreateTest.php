<?php

use backend\models\CategoriesForm;
use blog\entities\category\Category;
use blog\managers\CategoryManager;
use blog\repositories\category\CategoriesRepository;
use blog\repositories\users\UsersRepository;
use blog\services\CategoryService;
use Codeception\Test\Unit;
use common\fixtures\ActiveCategoriesFixture;
use common\fixtures\UserProfilesFixture;
use common\fixtures\UserFixture;
use common\models\active\CategoriesActive;

/**
 * Class CategoryCreateTest
 *
 * @property CategoriesRepository $repository
 * @property UsersRepository $userRepository
 * @property CategoryManager $manager
 * @property CategoriesActive $fc
 * @property \Faker\Generator $faker
 * @property UnitTester $tester
 */
class CategoryCreateTest extends Unit
{
    protected $tester;
    private $repository;
    private $userRepository;
    private $manager;
    private $faker;

    public function _setUp()
    {
        $this->faker = Faker\Factory::create();
        $this->repository = new CategoriesRepository(Yii::$app->db);
        $this->userRepository = new UsersRepository(Yii::$app->db);
        $this->manager = new CategoryManager($this->repository, new CategoryService());

        return parent::_setUp();
    }

    protected function _before()
    {
        $this->tester->haveFixtures([
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php'
            ],
            'profile' => [
                'class' => UserProfilesFixture::class,
                'dataFile' => codecept_data_dir() . 'profile.php'
            ],
            'category' => [
                'class' => ActiveCategoriesFixture::class,
                'dataFile' => codecept_data_dir() . 'activeCategories.php'
            ],
        ]);
    }

    public function testRelation()
    {
        /* @var $category Category */
        $category = $this->repository->getOneById(1);

        expect($category->getCreator()->getUserName())->notNull();
        expect($category->getCreator()->getProfile()->getBio())->notNull();
    }

    public function testFullCreate()
    {
        $createForm = new CategoriesForm();
        $createForm->title = $this->faker->title;
        $createForm->slug = $this->faker->slug;
        $createForm->description = $this->faker->text;
        $createForm->status = Category::STATUS_ACTIVE;
        $createForm->meta_title = $this->faker->title;
        $createForm->meta_keywords = $this->faker->sentence(5, true);
        $createForm->meta_description = $this->faker->text();

        $user = $this->tester->grabFixture('user', 0);
        $user = $this->userRepository->getOneById($user->id);

        $category = $this->manager->create($user, $createForm);

        expect($category->getTitle())->equals($createForm->title);
        expect($category->getMetaData()->getTitle())->equals($createForm->meta_title);
        expect($category->getPrimaryKey())->greaterThan(0);
        expect($category->isActive())->true();
        expect($category->isNew())->false();
    }

    public function testWithoutMeta()
    {
        $createForm = new CategoriesForm();
        $createForm->title = $this->faker->title;
        $createForm->slug = $this->faker->slug;
        $createForm->description = $this->faker->text;
        $createForm->status = Category::STATUS_ACTIVE;

        $user = $this->tester->grabFixture('user', 0);
        $user = $this->userRepository->getOneById($user->id);

        $category = $this->manager->create($user, $createForm);

        expect($category->getMetaData()->getTitle())->null();
    }

    public function testCreateInactive()
    {
        $createForm = new CategoriesForm();
        $createForm->title = $this->faker->title;
        $createForm->slug = $this->faker->slug;
        $createForm->description = $this->faker->text;
        $createForm->status = Category::STATUS_INACTIVE;

        $user = $this->tester->grabFixture('user', 0);
        $user = $this->userRepository->getOneById($user->id);

        $category = $this->manager->create($user, $createForm);

        expect($category->isActive())->false();
    }
}