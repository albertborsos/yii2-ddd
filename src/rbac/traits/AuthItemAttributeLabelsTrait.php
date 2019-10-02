<?php

namespace albertborsos\ddd\rbac\traits;

use Yii;

trait AuthItemAttributeLabelsTrait
{
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('auth-item', 'Name'),
            'type' => Yii::t('auth-item', 'Type'),
            'description' => Yii::t('auth-item', 'Description'),
            'ruleName' => Yii::t('auth-item', 'Rule Name'),
            'data' => Yii::t('auth-item', 'Data'),
            'createdAt' => Yii::t('auth-item', 'Created At'),
            'updatedAt' => Yii::t('auth-item', 'Updated At'),
        ];
    }
}
