<?php

namespace albertborsos\ddd\tests\support\base\domains\customer\mysql;

use albertborsos\ddd\repositories\AbstractActiveRepository;
use albertborsos\ddd\tests\support\base\InvalidHydrator;
use albertborsos\ddd\data\ActiveEntityDataProvider;
use albertborsos\ddd\tests\support\base\domains\customer\interfaces\CustomerActiveRepositoryInterface;
use yii\base\Model;
use yii\data\BaseDataProvider;

class InvalidHydratorCustomerActiveRepository extends CustomerActiveRepository
{
    protected $hydrator = InvalidHydrator::class;
}
