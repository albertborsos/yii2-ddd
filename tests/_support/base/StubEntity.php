<?php

namespace albertborsos\ddd\tests\support\base;

use albertborsos\ddd\models\AbstractEntity;
use yii\base\Model;

class StubEntity extends AbstractEntity
{
    /**
     * Mapping of property keys to entity classnames.
     *
     * @return array
     */
    public function relationMapping(): array
    {
        return [];
    }
}
