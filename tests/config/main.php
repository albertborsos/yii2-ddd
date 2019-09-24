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
        'cycle' => [
            'class' => \albertborsos\cycle\Connection::class,
            'dsn' => getenv('DB_TEST_DSN'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'schema' => [
                'customer' => \albertborsos\ddd\tests\support\base\infrastructure\cycle\customer\CustomerRepository::schema(),
                'customer_address' => \albertborsos\ddd\tests\support\base\infrastructure\cycle\customer\CustomerAddressRepository::schema(),
                'customer_with_behaviors' => \albertborsos\ddd\tests\support\base\infrastructure\cycle\customer\CustomerWithBehaviorsRepository::schema(),
                'customer_with_modified_behaviors' => \albertborsos\ddd\tests\support\base\infrastructure\cycle\customer\CustomerWithModifiedBehaviorsRepository::schema(),
                'page' => \albertborsos\ddd\tests\support\base\infrastructure\cycle\page\PageRepository::schema(),
                'page_slug' => \albertborsos\ddd\tests\support\base\infrastructure\cycle\page\PageSlugRepository::schema(),
            ],
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'user' => \albertborsos\ddd\tests\support\base\UserMock::class,
    ],
    'container' => [
        'definitions' => [
            \albertborsos\ddd\interfaces\HydratorInterface::class => \albertborsos\ddd\hydrators\ZendHydrator::class,
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\cycle\customer\CustomerRepository::class,
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerCacheUpdaterInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\cache\customer\CustomerRepository::class,
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerAddressRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\cycle\customer\CustomerAddressRepository::class,
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerAddressCacheUpdaterInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\cache\customer\CustomerAddressRepository::class,
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerWithBehaviorsRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\cycle\customer\CustomerWithBehaviorsRepository::class,
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerWithModifiedBehaviorsRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\cycle\customer\CustomerWithModifiedBehaviorsRepository::class,
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\InvalidEntityCustomerRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\cycle\customer\InvalidEntityCustomerRepository::class,
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\cycle\page\PageRepository::class,
            \albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageSlugRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\cycle\page\PageSlugRepository::class,
        ],
    ],
];

$localConfigFile = dirname(__FILE__) . '/main.local.php';

$localConfig = [];
if (is_file($localConfigFile)) {
    $localConfig = require($localConfigFile);
}

return \yii\helpers\ArrayHelper::merge($config, $localConfig);
