<?php

namespace albertborsos\ddd\tests\support\base\domains\customer\mysql;

use albertborsos\ddd\repositories\AbstractActiveRepository;
use mito\cms\core\data\ActiveEntityDataProvider;
use albertborsos\ddd\tests\support\base\domains\customer\interfaces\CustomerActiveRepositoryInterface;
use yii\data\BaseDataProvider;

class CustomerWithBehaviorsActiveRepository extends CustomerActiveRepository
{
    protected $entityClass = \albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerWithBehaviors::class;
}
