<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\cycle\customer;

use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\InvalidEntityCustomerRepositoryInterface;
use yii\base\Model;

class InvalidEntityCustomerRepository extends CustomerRepository implements InvalidEntityCustomerRepositoryInterface
{
    protected $entityClass = Model::class;
}
