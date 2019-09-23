<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\cycle\customer;

use albertborsos\ddd\tests\support\base\InvalidHydrator;

class InvalidHydratorCustomerRepository extends CustomerRepository
{
    protected $hydrator = InvalidHydrator::class;
}
