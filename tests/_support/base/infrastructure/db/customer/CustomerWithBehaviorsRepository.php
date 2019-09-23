<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\db\customer;

use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerWithBehaviorsRepositoryInterface;

class CustomerWithBehaviorsRepository extends CustomerRepository implements CustomerWithBehaviorsRepositoryInterface
{
    protected $entityClass = \albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerWithBehaviors::class;
}
