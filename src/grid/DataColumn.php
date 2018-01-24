<?php

namespace albertborsos\ddd\grid;

use albertborsos\ddd\data\ResourceDataProvider;
use yii\helpers\ArrayHelper;

class DataColumn extends \yii\grid\DataColumn
{
    private $_isResourceProvider = false;

    const RESOURCE_ATTRIBUTE_PREFIX = 'model.';

    public function init()
    {
        parent::init();
        $this->_isResourceProvider = $this->grid->dataProvider instanceof ResourceDataProvider;
    }

    /**
     * Returns the data cell value.
     * @param mixed $model the data model
     * @param mixed $key the key associated with the data model
     * @param int $index the zero-based index of the data model among the models array returned by [[GridView::dataProvider]].
     * @return string the data cell value
     */
    public function getDataCellValue($model, $key, $index)
    {
        if ($this->value !== null) {
            if (is_string($this->value)) {
                return ArrayHelper::getValue($model, $this->value);
            } else {
                return call_user_func($this->value, $model, $key, $index, $this);
            }
        } elseif ($this->attribute !== null) {
            if ($this->_isResourceProvider) {
                return ArrayHelper::getValue($model, self::RESOURCE_ATTRIBUTE_PREFIX . $this->attribute);
            }
            return ArrayHelper::getValue($model, $this->attribute);
        }
        return null;
    }
}
