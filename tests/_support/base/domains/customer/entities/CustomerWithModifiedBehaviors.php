<?php

namespace albertborsos\ddd\tests\support\base\domains\customer\entities;

use albertborsos\ddd\behaviors\BlameableBehavior;
use albertborsos\ddd\behaviors\TimestampBehavior;
use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\models\AbstractEntity;

/**
 * Class Customer
 * @package albertborsos\ddd\tests\support\base\domains\customer\entities
 */
class CustomerWithModifiedBehaviors extends CustomerWithBehaviors
{
    public function behaviors()
    {
        return [
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
        ];
    }
}
