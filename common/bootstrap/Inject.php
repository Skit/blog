<?php declare(strict_types=1);

namespace common\bootstrap;

use blog\managers\CategoryManager;
use blog\managers\PostManager;
use blog\repositories\category\CategoriesRepository;
use blog\repositories\post\PostRepository;
use blog\services\CategoryService;
use Yii;
use yii\base\BootstrapInterface;
use yii\db\Connection;

class Inject implements BootstrapInterface
{
    /**
     * @inheritDoc
     */
    public function bootstrap($app)
    {
        $container = Yii::$container;

        /**
         * CategoryManager
         */
        $container->setSingleton(CategoryManager::class, function (): CategoryManager
        {
            // TODO передать обертку на Connection или Command
            return new CategoryManager(new CategoriesRepository(new Connection()), new CategoryService());
        });

        /**
         * PostManager
         */
        $container->setSingleton(PostManager::class, function (): PostManager
        {
            $connection = new Connection();
            return new PostManager(new PostRepository($connection), new CategoriesRepository($connection), new CategoryService());
        });
    }
}