<?php

namespace albertborsos\ddd\behaviors;

use albertborsos\ddd\traits\EvaluateAttributesTrait;

class BlameableBehavior extends \yii\behaviors\BlameableBehavior
{
    use EvaluateAttributesTrait;

    public $createdByAttribute = 'createdBy';

    public $updatedByAttribute = 'updatedBy';
}
