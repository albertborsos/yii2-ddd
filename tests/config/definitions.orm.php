<?php
return [
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
    \albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\UpdatePageServiceInterface::class => \albertborsos\ddd\tests\support\base\services\page\UpdatePageOrmService::class,
    \albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageSluggableBehaviorInterface::class => \albertborsos\ddd\tests\support\base\domains\page\behaviors\PageSluggableOrmBehavior::class,
];
