<?php

namespace albertborsos\ddd\rbac\traits;

use Yii;

trait AuthItemChildAttributeLabelsTrait
{
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'parent' => Yii::t('auth-item-child', 'Parent'),
            'child' => Yii::t('auth-item-child', 'Child'),
        ];
    }
}
