<?php declare(strict_types=1);

namespace blog\managers;

use backend\models\CategoriesForm;
use blog\entities\category\Category;
use blog\entities\common\exceptions\BlogRecordsException;
use blog\entities\common\exceptions\MetaDataExceptions;
use blog\entities\user\User;
use blog\repositories\category\CategoriesRepository;
use blog\repositories\exceptions\RepositoryException;
use blog\services\CategoryService;

/**
 * Class CategoryManager
 * @package blog\managers
 */
class CategoryManager
{
    private $repository, $service;

    /**
     * CategoryManager constructor.
     * @param CategoriesRepository $repository
     * @param CategoryService $service
     */
    public function __construct(CategoriesRepository $repository, CategoryService $service)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * @param User $creator
     * @param CategoriesForm $form
     * @return Category
     * @throws MetaDataExceptions
     * @throws RepositoryException
     * @throws BlogRecordsException
     */
    public function create(User $creator, CategoriesForm $form): Category
    {
        $category = Category::create(
            $form->title,
            $form->slug,
            $form->content,
            $this->service->setMetaData($form),
            $creator,
            $form->status
        );

        $category->setPrimaryKey($this->repository->create($category));

        return $category;
    }

    /**
     * @param $category
     * @param CategoriesForm $form
     * @return Category
     * @throws \yii\db\Exception
     */
    public function edit($category, CategoriesForm $form): Category
    {
        // FIXME если вернет bool выкинуть 404
        // FIXME фикстуры в тестах возвращают экземпляр модели, переделать, чтобы сюда попадал экземпляр категории
        $category = $this->repository->findOneById($category->id, $category->status);
        $category->edit($form->title, $form->slug, $form->content, $this->service->setMetaData($form), $form->status);
        $this->repository->update($category);

        return $category;
    }
}