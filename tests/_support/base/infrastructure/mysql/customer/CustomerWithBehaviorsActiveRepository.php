<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\mysql\customer;

use albertborsos\ddd\repositories\AbstractActiveRepository;
use albertborsos\ddd\data\ActiveEntityDataProvider;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerActiveRepositoryInterface;
use yii\data\BaseDataProvider;

class CustomerWithBehaviorsActiveRepository extends CustomerActiveRepository
{
    protected $entityClass = \albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerWithBehaviors::class;
}
