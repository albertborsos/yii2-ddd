<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer;

interface CustomerCacheUpdaterInterface
{
    public function updateVipCustomers($customers);
}
