<?php

namespace albertborsos\ddd\traits;

use albertborsos\ddd\models\AbstractEntity;
use albertborsos\ddd\repositories\AbstractCycleRepository;

trait DefaultFilterConditionTrait
{
    public $filter = [];

    protected function defaultFilterCondition(AbstractEntity $entity)
    {
        $condition = [];

        if ($entity->isNew()) {
            return $condition;
        }

        $primaryKeys = is_array($entity->getPrimaryKey()) ? $entity->getPrimaryKey() : [$entity->getPrimaryKey()];
        array_walk($primaryKeys, function ($key) use (&$condition, $entity) {
            switch (true) {
                case $this->targetRepository instanceof AbstractCycleRepository:
                    $condition[] = [$key, '!=', $entity->{$key}];
                    break;
            }
        });

        return $condition;
    }
}
