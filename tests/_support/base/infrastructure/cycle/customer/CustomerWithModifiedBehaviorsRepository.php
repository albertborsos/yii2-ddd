<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\cycle\customer;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerWithModifiedBehaviors;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerWithModifiedBehaviorsRepositoryInterface;
use Cycle\ORM\Schema;
use yii\base\ModelEvent;

class CustomerWithModifiedBehaviorsRepository extends CustomerWithBehaviorsRepository implements CustomerWithModifiedBehaviorsRepositoryInterface
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

    public static function schema(): array
    {
        $schema = parent::schema();
        $schema[Schema::ENTITY] = CustomerWithModifiedBehaviors::class;
        return $schema;
    }
}
