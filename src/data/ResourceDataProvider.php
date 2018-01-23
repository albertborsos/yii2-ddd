<?php

namespace albertborsos\ddd\data;

use yii\data\ActiveDataProvider;

/**
 * Class ResourceDataProvider
 * @package albertborsos\ddd\data
 */
class ResourceDataProvider extends ActiveDataProvider
{
    /**
     * @var string
     */
    public $resourceClass;

    /**
     * @inheritdoc
     */
    protected function prepareModels()
    {
        $models = parent::prepareModels();

        $result = [];
        foreach ($models as $model) {
            $result[] = \Yii::createObject($this->resourceClass, [null, $model]);
        }

        return $result;
    }
}
