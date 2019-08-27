<?php

namespace albertborsos\ddd\behaviors;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\traits\EvaluateAttributesTrait;

class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{
    use EvaluateAttributesTrait;

    public $createdAtAttribute = 'createdAt';

    public $updatedAtAttribute = 'updatedAt';

    public function init()
    {
        $this->setDefaultAttributes();
        parent::init();
    }

    protected function setDefaultAttributes(): void
    {
        if (!empty($this->attributes)) {
            return;
        }

        $this->attributes = [
            EntityInterface::EVENT_BEFORE_INSERT => [$this->createdAtAttribute, $this->updatedAtAttribute],
            EntityInterface::EVENT_BEFORE_UPDATE => $this->updatedAtAttribute,
        ];
    }
}
