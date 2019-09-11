<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\db\customer;

class CustomerWithBehaviorsRepository extends CustomerRepository
{
    protected $entityClass = \albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerWithBehaviors::class;
}
