<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer;

use albertborsos\ddd\interfaces\CacheRepositoryInterface;

interface CustomerCacheRepositoryInterface extends CacheRepositoryInterface
{
    public function getVipCustomers();

    public function updateVipCustomers($customers);
}
