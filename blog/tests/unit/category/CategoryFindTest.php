<?php
namespace blog\tests;


use blog\entities\category\Category;
use blog\repositories\category\CategoriesRepository;
use blog\repositories\exceptions\RepositoryException;
use Codeception\Test\Unit;
use common\fixtures\ActiveCategoriesFixture;
use common\fixtures\InactiveCategoriesFixture;
use common\fixtures\UserFixture;
use common\models\active\CategoriesActive;
use UnitTester;
use Yii;

/**
 * Class CategoryFindTest
 *
 * @property CategoriesRepository $repository
 * @property CategoriesActive $fc
 * @property UnitTester $tester
 *
 * @package blog\tests
 */
class CategoryFindTest extends Unit
{
    protected $tester;
    private $fc;
    private $repository;

    public function _setUp()
    {
        $this->repository = new CategoriesRepository(Yii::$app->db);
        return parent::_setUp();
    }

    protected function _before()
    {
        $this->tester->haveFixtures([
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php'
            ],
            'active' => [
                'class' => ActiveCategoriesFixture::class,
                'dataFile' => codecept_data_dir() . 'activeCategories.php'
            ],
            'inactive' => [
                'class' => InactiveCategoriesFixture::class,
                'dataFile' => codecept_data_dir() . 'inactiveCategories.php'
            ],
        ]);
    }

    public function testCheckGettingData()
    {
        $this->fc = $this->tester->grabFixture('active', 1);

        expect($this->fc->id)->int();
        expect($this->fc->status)->int();
        expect($this->fc->title)->string();
        expect($this->fc->slug)->string();
        expect($this->fc->content)->string();
        expect($this->fc->created_at)->string();
        expect($this->fc->meta_data)->array();
        expect($this->fc->updated_at)->null();
    }

    public function testActive()
    {
        $this->fc = $this->tester->grabFixture('active', 1);
        $category = $this->repository->findOneById($this->fc->id, Category::STATUS_ACTIVE);

        expect($category)->isInstanceOf(Category::class);
        expect($category->getMetaData()->getTitle())->equals($this->fc->meta_data['title']);
    }

    public function testInactiveAsActive()
    {
        $this->fc = $this->tester->grabFixture('inactive', 0);

        $this->expectException(RepositoryException::class);
        $this->repository->findOneById($this->fc->id, Category::STATUS_ACTIVE);
    }
}