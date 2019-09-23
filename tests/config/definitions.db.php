<?php
return [
    \albertborsos\ddd\interfaces\HydratorInterface::class => \albertborsos\ddd\hydrators\ActiveHydrator::class,
    \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\db\customer\CustomerRepository::class,
    \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerCacheUpdaterInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\cache\customer\CustomerRepository::class,
    \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerAddressRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\db\customer\CustomerAddressRepository::class,
    \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerAddressCacheUpdaterInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\cache\customer\CustomerAddressRepository::class,
    \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerWithBehaviorsRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\db\customer\CustomerWithBehaviorsRepository::class,
    \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerWithModifiedBehaviorsRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\db\customer\CustomerWithModifiedBehaviorsRepository::class,
    \albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\InvalidEntityCustomerRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\db\customer\InvalidEntityCustomerRepository::class,
    \albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\db\page\PageRepository::class,
    \albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageSlugRepositoryInterface::class => \albertborsos\ddd\tests\support\base\infrastructure\db\page\PageSlugRepository::class,
    \albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\UpdatePageServiceInterface::class => \albertborsos\ddd\tests\support\base\services\page\UpdatePageDbService::class,
    \albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageSluggableBehaviorInterface::class => \albertborsos\ddd\tests\support\base\domains\page\behaviors\PageSluggableDbBehavior::class,
];
