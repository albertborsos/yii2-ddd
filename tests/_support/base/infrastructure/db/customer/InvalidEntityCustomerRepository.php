<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\db\customer;

use yii\base\Model;

class InvalidEntityCustomerRepository extends CustomerRepository
{
    protected $entityClass = Model::class;
}
