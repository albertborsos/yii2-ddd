<?php

namespace albertborsos\ddd\tests\support\base\domains\customer\mysql;

use albertborsos\ddd\repositories\AbstractActiveRepository;
use mito\cms\core\data\ActiveEntityDataProvider;
use albertborsos\ddd\tests\support\base\domains\customer\interfaces\CustomerActiveRepositoryInterface;
use yii\base\Model;
use yii\data\BaseDataProvider;

class InvalidEntityCustomerActiveRepository extends CustomerActiveRepository
{
    protected $entityClass = Model::class;
}
