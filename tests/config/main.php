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
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\db\customer\CustomerRepository::class,
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerCacheUpdaterInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\cache\customer\CustomerRepository::class,
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerAddressRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\db\customer\CustomerAddressRepository::class,
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerAddressCacheUpdaterInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\cache\customer\CustomerAddressRepository::class,
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\db\page\PageRepository::class,
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageSlugRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\db\page\PageSlugRepository::class,
        ],
    ],
];

$localConfigFile = dirname(__FILE__) . '/main.local.php';

$localConfig = [];
if (is_file($localConfigFile)) {
    $localConfig = require($localConfigFile);
}

return \yii\helpers\ArrayHelper::merge($config, $localConfig);
