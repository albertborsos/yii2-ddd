<?php

namespace albertborsos\ddd\tests\support\base;

use Yii;

abstract class AbstractBaseResourceTest extends \yii\codeception\DbTestCase
{
    protected $resourceClass;
    protected $modelClass;

    /**
     * @param \app\models\FormObject|null $formObject
     * @param \app\models\BusinessObject|null $businessObject
     * @return object|\app\models\AbstractResource
     */
    protected function mockResource(\app\models\FormObject $formObject = null, \app\models\BusinessObject $businessObject = null)
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
