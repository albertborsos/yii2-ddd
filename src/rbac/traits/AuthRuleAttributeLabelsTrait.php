<?php

namespace albertborsos\ddd\rbac\traits;

use Yii;

trait AuthRuleAttributeLabelsTrait
{
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('auth-rule', 'Name'),
            'data' => Yii::t('auth-rule', 'Data'),
            'createdAt' => Yii::t('auth-rule', 'Created At'),
            'updatedAt' => Yii::t('auth-rule', 'Updated At'),
        ];
    }
}
