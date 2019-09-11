<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\cache\customer;

use albertborsos\ddd\repositories\CacheRepository;
use albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerAddress;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerAddressCacheUpdaterInterface;

class CustomerAddressRepository extends CacheRepository implements CustomerAddressCacheUpdaterInterface
{
    protected $entityClass = CustomerAddress::class;
}
