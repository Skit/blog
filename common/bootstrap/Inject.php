<?php declare(strict_types=1);

namespace common\bootstrap;

use blog\managers\CategoryManager;
use blog\services\CategoryService;
use Yii;
use blog\repositories\category\CategoriesRepository;
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

        $container->setSingleton(CategoryManager::class, function (): CategoryManager
        {
            // TODO передать обертку на Connection или Command
            return new CategoryManager(new CategoriesRepository(new Connection()), new CategoryService());
        });
    }
}