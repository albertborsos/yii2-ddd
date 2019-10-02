<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer;

use albertborsos\ddd\interfaces\RepositoryInterface;

interface CustomerRepositoryInterface extends RepositoryInterface
{
    public function getVipCustomers();
}
