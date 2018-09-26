<?php

namespace albertborsos\ddd\data;

use yii\data\ActiveDataProvider;

/**
 * Class ResourceDataProvider
 * @deprecated since 0.3.0, will be removed in 1.0.0.
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
