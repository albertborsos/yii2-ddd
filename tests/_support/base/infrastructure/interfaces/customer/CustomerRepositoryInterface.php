<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer;

use albertborsos\ddd\interfaces\ActiveRepositoryInterface;

interface CustomerRepositoryInterface
{
    public function getVipCustomers();
}