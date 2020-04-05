<?php
use AspectMock\Kernel;

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');
defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', __DIR__.'/../../');

require_once YII_APP_BASE_PATH . '/vendor/autoload.php';
$kernel = Kernel::getInstance();
$kernel->init([
    'debug' => true,
    'cacheDir' => '/var/tmp',
    'includePaths' => [__DIR__.'/../../www']
]);

require_once YII_APP_BASE_PATH . '/vendor/yiisoft/yii2/Yii.php';
require_once YII_APP_BASE_PATH . '/common/config/bootstrap.php';