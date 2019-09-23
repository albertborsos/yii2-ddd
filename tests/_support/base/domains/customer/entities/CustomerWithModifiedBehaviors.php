<?php

namespace albertborsos\ddd\tests\support\base\domains\customer\entities;

use albertborsos\ddd\behaviors\BlameableBehavior;
use albertborsos\ddd\behaviors\SluggableBehavior;
use albertborsos\ddd\behaviors\TimestampBehavior;
use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\models\AbstractEntity;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerWithModifiedBehaviorsRepositoryInterface;

/**
 * Class Customer
 * @package albertborsos\ddd\tests\support\base\domains\customer\entities
 */
class CustomerWithModifiedBehaviors extends CustomerWithBehaviors
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    EntityInterface::EVENT_BEFORE_INSERT => ['createdAt'],
                ],
            ],
            'blameable' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    EntityInterface::EVENT_BEFORE_INSERT => ['createdBy'],
                ],
            ],
            'sluggable' => [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'ensureUnique' => true,
                'repository' => CustomerWithModifiedBehaviorsRepositoryInterface::class,
            ],
        ]);
    }
}
