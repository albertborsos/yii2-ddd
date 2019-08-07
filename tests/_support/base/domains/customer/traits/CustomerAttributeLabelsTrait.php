<?php

namespace albertborsos\ddd\tests\support\base\domains\customer\traits;

use Yii;

trait CustomerAttributeLabelsTrait
{
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }
}
