<?php

namespace albertborsos\ddd\tests\support\base;

use Yii;

abstract class AbstractServiceTest extends Unit
{
    protected $formClass;
    protected $serviceClass;
    protected $modelClass;

    /**
     * @param array $loadParams
     * @param \albertborsos\ddd\interfaces\BusinessObject|null $businessObject
     * @param array $initParams
     * @return \albertborsos\ddd\interfaces\FormObject|\yii\base\Model
     */
    protected function mockForm($loadParams = [], \albertborsos\ddd\interfaces\BusinessObject $businessObject = null, $initParams = [], $isInitParamsAreArguments = false)
    {
        if ($isInitParamsAreArguments) {
            $params = $businessObject ? array_merge([$businessObject], $initParams) : $initParams;
        } else {
            $params = $businessObject ? [$businessObject, $initParams] : [$initParams];
        }

        /** @var \yii\base\Model|\albertborsos\ddd\interfaces\FormObject $form */
        $form = Yii::createObject($this->formClass, $params);
        $form->load($loadParams, '');

        return $form;
    }

    /**
     * @param \albertborsos\ddd\interfaces\FormObject $formObject
     * @param \albertborsos\ddd\interfaces\BusinessObject|null $businessObject
     * @return \albertborsos\ddd\models\AbstractService
     */
    protected function mockService(\albertborsos\ddd\interfaces\FormObject $formObject, \albertborsos\ddd\interfaces\BusinessObject $businessObject = null)
    {
        return Yii::createObject($this->serviceClass, [$formObject, $businessObject]);
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
