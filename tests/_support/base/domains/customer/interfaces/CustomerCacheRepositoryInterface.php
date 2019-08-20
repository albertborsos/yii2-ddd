<?php

namespace albertborsos\ddd\tests\support\base\domains\customer\interfaces;

use albertborsos\ddd\interfaces\CacheRepositoryInterface;

interface CustomerCacheRepositoryInterface extends CacheRepositoryInterface
{
    public function getVipCustomers();

    public function updateVipCustomers($customers);
}
