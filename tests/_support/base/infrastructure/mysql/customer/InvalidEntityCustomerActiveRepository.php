<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\mysql\customer;

use albertborsos\ddd\repositories\AbstractActiveRepository;
use albertborsos\ddd\data\ActiveEntityDataProvider;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerActiveRepositoryInterface;
use yii\base\Model;
use yii\data\BaseDataProvider;

class InvalidEntityCustomerActiveRepository extends CustomerActiveRepository
{
    protected $entityClass = Model::class;
}
