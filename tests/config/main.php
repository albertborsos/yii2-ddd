<?php

$config = [
    'id' => 'ddd-test',
    'basePath' => dirname(__DIR__),
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => getenv('DB_TEST_DSN'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'charset' => 'utf8',
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'user' => \albertborsos\ddd\tests\support\base\UserMock::class,
    ],
    'container' => [
        'definitions' => [
            \albertborsos\ddd\interfaces\HydratorInterface::class => \albertborsos\ddd\hydrators\ActiveHydrator::class,
            \albertborsos\ddd\tests\support\base\domains\customer\interfaces\CustomerActiveRepositoryInterface::class => \albertborsos\ddd\tests\support\base\domains\customer\mysql\CustomerActiveRepository::class,
            \albertborsos\ddd\tests\support\base\domains\customer\interfaces\CustomerCacheRepositoryInterface::class => \albertborsos\ddd\tests\support\base\domains\customer\cache\CustomerCacheRepository::class,
            \albertborsos\ddd\tests\support\base\domains\customer\interfaces\CustomerAddressActiveRepositoryInterface::class => \albertborsos\ddd\tests\support\base\domains\customer\mysql\CustomerAddressActiveRepository::class,
            \albertborsos\ddd\tests\support\base\domains\customer\interfaces\CustomerAddressCacheRepositoryInterface::class => \albertborsos\ddd\tests\support\base\domains\customer\cache\CustomerAddressCacheRepository::class,
        ],
    ],
];

$localConfigFile = dirname(__FILE__) . '/main.local.php';

$localConfig = [];
if (is_file($localConfigFile)) {
    $localConfig = require($localConfigFile);
}

return \yii\helpers\ArrayHelper::merge($config, $localConfig);
