<?php

namespace blog\tests\unit\category;

use blog\entities\category\Category;
use blog\entities\common\MetaData;
use blog\entities\user\User;
use Codeception\Stub;
use Codeception\Test\Unit;

class CategoryTest extends Unit
{
    /* @var $activeUser User */
    private $activeUser;

    public function setUp(): void
    {
        $this->activeUser = Stub::make(User::class, ['username' => 'Bob' ,'status' => User::STATUS_ACTIVE, 'id' => 1]);
        parent::setUp();
    }

    public function testCreateFull()
    {
        $metaData = new MetaData('some cat seo title');

        $category = Category::create('Title', 'slug', 'Desc', $metaData, $this->activeUser, Category::STATUS_ACTIVE);

        expect($category->getSlug())->equals('slug');
        expect($category->getTitle())->equals('Title');
        expect($category->getContent())->equals('Desc');
        expect($category->getMetaData()->getTitle())->equals('some cat seo title');
        expect($category->getMetaData()->getDescription())->null();
        expect($category->getMetaData()->getKeywords())->null();
    }

    public function testCreateWithInactiveUser()
    {
        $this->expectExceptionMessage('User must be active for this operation');
        Category::create('', '', '', new MetaData(), new User(), Category::STATUS_ACTIVE);
    }

    public function testActivate()
    {
        $category = Stub::make(Category::class, ['status' => Category::STATUS_INACTIVE]);

        $category->activate();
        expect($category->isActive())->true();
    }

    public function testCreateWithEmptyMetaData()
    {
        $category = Stub::make(Category::class, ['meta_data' => new MetaData()]);

        expect($category->getMetaData()->getTitle())->null();
        expect($category->getMetaData()->getDescription())->null();
        expect($category->getMetaData()->getKeywords())->null();
    }

    public function testEdit()
    {
        $category = Category::create('Title', 'slug', 'Desc', new MetaData(), $this->activeUser, Category::STATUS_INACTIVE);

        $category->edit(
            'Edit title',
            'Edit slug',
            'Edit description',
            new MetaData('Edit cat seo title', 'Edit seo desc', 'Edit category key'),
            Category::STATUS_ACTIVE
        );

        expect($category->getTitle())->equals('Edit title');
        expect($category->getSlug())->equals('Edit slug');
        expect($category->getContent())->equals('Edit description');
        expect($category->getMetaData()->getTitle())->equals('Edit cat seo title');
        expect($category->getMetaData()->getKeywords())->equals('Edit category key');
        expect($category->getMetaData()->getDescription())->equals('Edit seo desc');
        expect($category->isActive())->true();
    }

    public function testWrongActivate()
    {
        $category = $category = Stub::make(Category::class, ['status' => Category::STATUS_ACTIVE]);

        $this->expectExceptionMessage('Record is already active');
        $category->activate();
    }

    public function testWrongDeactivate()
    {
        $category = $category = Stub::make(Category::class, ['status' => Category::STATUS_INACTIVE]);

        $this->expectExceptionMessage('Record is already inactive');
        $category->deactivate();
    }

    public function testGetCreator()
    {
        $category = Category::create('Title', 'slug', 'Desc', new MetaData(), $this->activeUser, Category::STATUS_INACTIVE);

        expect($category->getCreator()->isActive())->true();
        expect($category->getCreator()->getUsername())->equals('Bob');
    }
}