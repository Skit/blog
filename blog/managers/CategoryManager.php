<?php declare(strict_types=1);

namespace blog\managers;

use backend\models\CategoriesForm;
use blog\entities\category\Category;
use blog\entities\category\exceptions\CategoryException;
use blog\entities\common\abstracts\HasRelation;
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
     * @param CategoriesForm $model
     * @return Category
     * @throws CategoryException
     */
    public function create(User $creator, CategoriesForm $model): Category
    {
        $category = Category::create(
            $model->title,
            $model->slug,
            $model->description,
            $this->service->setMetaData($model),
            $creator,
            $model->status
        );

        return $this->repository->create($category);
    }

    /**
     * @param int $id
     * @param CategoriesForm $model
     * @return Category
     * @throws RepositoryException
     * @throws MetaDataExceptions
     */
    public function edit(int $id, CategoriesForm $model): Category
    {
        $category = $this->repository->getOneById($id);
        $category->edit($model->title, $model->slug, $model->description, $this->service->setMetaData($model), $model->status);
        $this->repository->update($category);

        return $category;
    }
}