<?php

namespace albertborsos\ddd\tests\support\base\domains\customer\cache;

use albertborsos\ddd\repositories\CacheRepository;
use albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerAddress;
use albertborsos\ddd\tests\support\base\domains\customer\interfaces\CustomerAddressCacheRepositoryInterface;

class CustomerAddressCacheRepository extends CacheRepository implements CustomerAddressCacheRepositoryInterface
{
    protected $entityClass = CustomerAddress::class;
}
