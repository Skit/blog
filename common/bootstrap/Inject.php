<?php declare(strict_types=1);

namespace common\bootstrap;

use blog\components\ImageResizer\{ImageResizer, driver\ImagickDriver};
use blog\components\ImageResizer\settings\{ImagickSettings, Jpeg, Modulate, Resize, Sharp};
use blog\entities\post\PostHighlighter;
use blog\managers\{AssignManager, CategoryManager, PostManager, TagManager};
use blog\repositories\category\CategoriesRepository;
use blog\repositories\post\PostRepository;
use blog\repositories\postTag\PostTagRepository;
use blog\repositories\tag\TagRepository;
use blog\repositories\users\UsersRepository;
use blog\services\{AssignService, CategoryService, PostService, TagService};
use common\components\{MCommand, MConnection, MTransaction};
use Imagick;
use PDO;
use Yii;
use yii\base\BootstrapInterface;

/**
 * TODO для фронта сделать свои зависимости эти перенести в бэк
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
                new PostService(new PostHighlighter()));
        });

        /**
         * ImageResizer
         */
        $container->setSingleton(ImageResizer::class, function (): ImageResizer
        {
            return new ImageResizer(
                new ImagickDriver(
                    new ImagickSettings(
                        new Jpeg(82),
                        new Resize(0.99, true, Imagick::FILTER_TRIANGLE),
                        new Sharp(0.25,0.25, 25, 0.005),
                        new Modulate(80, 60, 100)
                    )
                )
            );
        });
    }
}