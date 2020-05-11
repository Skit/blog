<?php declare(strict_types=1);

namespace common\bootstrap;

use blog\managers\AssignManager;
use blog\managers\CategoryManager;
use blog\managers\PostManager;
use blog\managers\TagManager;
use blog\repositories\category\CategoriesRepository;
use blog\repositories\post\PostRepository;
use blog\repositories\postTag\PostTagRepository;
use blog\repositories\tag\TagRepository;
use blog\repositories\users\UsersRepository;
use blog\services\AssignService;
use blog\services\CategoryService;
use blog\services\PostService;
use blog\services\TagService;
use common\components\MCommand;
use common\components\MConnection;
use common\components\MTransaction;
use PDO;
use Yii;
use yii\base\BootstrapInterface;

/**
 * Class Inject
 * @package common\bootstrap
 */
class Inject implements BootstrapInterface
{
    /**
     * @inheritDoc
     */
    public function bootstrap($app)
    {
        $container = Yii::$container;

        /**
         * MConnection
         */
        $container->setSingleton(MConnection::class, function (): MConnection
        {
            $connection =  new MConnection();

            $connection->charset = 'utf8mb4';
            $connection->username = 'root';
            $connection->password = 'secret';
            $connection->attributes = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
            $connection->commandMap['mysql'] = MCommand::class;
            $connection->dsn = 'mysql:host=blog_mysql_1;dbname=blog_test';

            return $connection;
        });

        /**
         * CategoryManager
         */
        $container->setSingleton(CategoryManager::class, function (): CategoryManager
        {
            $connection = Yii::$container->get(MConnection::class);

            return new CategoryManager(new CategoriesRepository($connection), new CategoryService());
        });

        /**
         * PostManager
         */
        $container->setSingleton(PostManager::class, function (): PostManager
        {
            $connection = Yii::$container->get(MConnection::class);

            return new PostManager(
                new PostRepository($connection),
                new CategoriesRepository($connection),
                new UsersRepository($connection),
                new TagRepository($connection),
                new AssignManager(new PostTagRepository($connection), new AssignService()),
                new TagManager(new TagRepository($connection), new TagService()),
                new MTransaction($connection),
                new PostService());
        });
    }
}