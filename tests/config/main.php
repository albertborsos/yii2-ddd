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
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerActiveRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\mysql\customer\CustomerActiveRepository::class,
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerCacheRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\cache\customer\CustomerCacheRepository::class,
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerAddressActiveRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\mysql\customer\CustomerAddressActiveRepository::class,
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerAddressCacheRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\cache\customer\CustomerAddressCacheRepository::class,
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageActiveRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\mysql\page\PageActiveRepository::class,
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageSlugActiveRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\mysql\page\PageSlugActiveRepository::class,
        ],
    ],
];

$localConfigFile = dirname(__FILE__) . '/main.local.php';

$localConfig = [];
if (is_file($localConfigFile)) {
    $localConfig = require($localConfigFile);
}

return \yii\helpers\ArrayHelper::merge($config, $localConfig);
