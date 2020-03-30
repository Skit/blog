<?php
namespace blog\tests;

use backend\models\CategoriesForm;
use blog\entities\category\Category;
use blog\managers\CategoryManager;
use blog\repositories\category\CategoriesRepository;
use blog\services\CategoryService;
use Codeception\Test\Unit;
use common\fixtures\ActiveCategoriesFixture;
use common\fixtures\InactiveCategoriesFixture;
use common\fixtures\UserFixture;
use common\models\active\CategoriesActive;
use UnitTester;
use Yii;

/**
 * Class CategoryEditTest
 *
 * @property CategoryManager $manager
 * @property CategoriesActive $fc
 * @property UnitTester $tester
 * @package blog\tests
 */
class CategoryEditTest extends Unit
{
    protected $tester;
    private $manager;
    private $fc;

    public function _setUp()
    {
        $this->manager = new CategoryManager(new CategoriesRepository(Yii::$app->db), new CategoryService());
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

    public function testUpdatedAt()
    {
        $updatedTimeTest = time();
        $this->fc = $this->tester->grabFixture('active', 1);

        $editForm = new CategoriesForm();
        $editForm->title = $this->fc->title;
        $editForm->slug = $this->fc->slug;
        $editForm->description = $this->fc->description;
        $editForm->status = $this->fc->status;

        $category = $this->manager->edit($this->fc->id, $editForm);
        expect($this->fc->updated_at)->null();
        expect($updatedTimeTest)->lessOrEquals(strtotime($category->getUpdatedAt()));
    }

    public function testMetaData()
    {
        $this->fc = $this->tester->grabFixture('active', 0);

        $editForm = new CategoriesForm();
        $editForm->title = $this->fc->title;
        $editForm->slug = $this->fc->slug;
        $editForm->description = $this->fc->description;
        $editForm->status = $this->fc->status;
        $editForm->meta_title = 'seo t';
        $editForm->meta_description = 'seo d';
        $editForm->meta_keywords = 'seo k';

        $category = $this->manager->edit($this->fc->id, $editForm);

        expect($category->getMetaData()->getTitle())->equals('seo t');
        expect($category->getMetaData()->getDescription())->equals('seo d');
        expect($category->getMetaData()->getKeywords())->equals('seo k');
    }

    public function testFullWithoutMeta()
    {
        $this->fc = $this->tester->grabFixture('active', 0);

        $editForm = new CategoriesForm();
        $editForm->title = $title = uniqid('t');
        $editForm->slug = $slug = uniqid('s');
        $editForm->description = $description = uniqid('d');
        $editForm->status = Category::STATUS_INACTIVE;

        $category = $this->manager->edit($this->fc->id, $editForm);

        expect($category->getTitle())->equals($title);
        expect($category->getSlug())->equals($slug);
        expect($category->getContent())->equals($description);
        expect($category->getMetaData()->getTitle())->null();
    }

    public function testStatus()
    {
        $this->fc = $this->tester->grabFixture('inactive', 0);

        $editForm = new CategoriesForm();
        $editForm->title = $this->fc->title;
        $editForm->slug = $this->fc->slug;
        $editForm->description = $this->fc->description;
        $editForm->status = Category::STATUS_ACTIVE;

        $category = $this->manager->edit($this->fc->id, $editForm);

        expect($category->isActive())->true();
    }
}
