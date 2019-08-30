<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\cache\customer;

use albertborsos\ddd\repositories\CacheRepository;
use albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerAddress;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerAddressCacheRepositoryInterface;

class CustomerAddressCacheRepository extends CacheRepository implements CustomerAddressCacheRepositoryInterface
{
    protected $entityClass = CustomerAddress::class;
}
