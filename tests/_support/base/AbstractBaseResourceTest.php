<?php

namespace albertborsos\ddd\tests\support\base;

use Yii;

/**
 * Class AbstractBaseResourceTest
 * @deprecated since 0.3.0, will be removed in 1.0.0.
 * @package albertborsos\ddd\tests\support\base
 */
abstract class AbstractBaseResourceTest extends Unit
{
    protected $resourceClass;
    protected $modelClass;

    /**
     * @param \albertborsos\ddd\interfaces\FormObject|null $formObject
     * @param \albertborsos\ddd\interfaces\BusinessObject|null $businessObject
     * @return object|\albertborsos\ddd\models\AbstractResource
     */
    protected function mockResource(\albertborsos\ddd\interfaces\FormObject $formObject = null, \albertborsos\ddd\interfaces\BusinessObject $businessObject = null)
    {
        return Yii::createObject($this->resourceClass, [$formObject, $businessObject]);
    }

    /**
     * @param $id
     * @return \yii\db\ActiveRecord
     */
    protected function getModel($id)
    {
        return call_user_func([$this->modelClass, 'findOne'], $id);
    }
}
