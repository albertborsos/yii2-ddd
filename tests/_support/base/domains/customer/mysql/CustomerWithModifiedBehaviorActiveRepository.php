<?php

namespace albertborsos\ddd\tests\support\base\domains\customer\mysql;

use albertborsos\ddd\interfaces\EntityInterface;
use yii\base\ModelEvent;

class CustomerWithModifiedBehaviorActiveRepository extends CustomerActiveRepository
{
    public $fakeEventClass = false;

    protected $entityClass = \albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerWithModifiedBehaviors::class;

    public function beforeSave(bool $insert, EntityInterface $entity, array $dirtyAttributes = [])
    {
        if (!$this->fakeEventClass) {
            return parent::beforeSave($insert, $entity, $dirtyAttributes);
        }

        $event = new ModelEvent();
        $entity->trigger($insert ? EntityInterface::EVENT_BEFORE_INSERT : EntityInterface::EVENT_BEFORE_UPDATE, $event);

        return $event->isValid;
    }
}
