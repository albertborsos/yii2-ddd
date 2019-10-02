<?php

namespace albertborsos\ddd\rbac\traits;

use Yii;

trait AuthAssignmentAttributeLabelsTrait
{
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'itemName' => Yii::t('auth-assignment', 'Item Name'),
            'userId' => Yii::t('auth-assignment', 'User ID'),
            'createdAt' => Yii::t('auth-assignment', 'Created At'),
        ];
    }
}
