<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\mysql\customer;

use albertborsos\ddd\repositories\AbstractActiveRepository;
use albertborsos\ddd\tests\support\base\InvalidHydrator;
use albertborsos\ddd\data\ActiveEntityDataProvider;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerActiveRepositoryInterface;
use yii\base\Model;
use yii\data\BaseDataProvider;

class InvalidHydratorCustomerActiveRepository extends CustomerActiveRepository
{
    protected $hydrator = InvalidHydrator::class;
}
