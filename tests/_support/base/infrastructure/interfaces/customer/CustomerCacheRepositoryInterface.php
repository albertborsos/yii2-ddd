<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer;

use albertborsos\ddd\interfaces\RepositoryInterface;

interface CustomerCacheRepositoryInterface extends RepositoryInterface
{
    public function getVipCustomers();

    public function updateVipCustomers($customers);
}
