<?php

namespace albertborsos\ddd\tests\support\base;

use Yii;

abstract class AbstractFormTest extends \yii\codeception\DbTestCase
{
    protected $formClass;
    protected $modelClass;

    /**
     * @param array $loadParams
     * @param \app\models\BusinessObject|null $businessObject
     * @param array $initParams
     * @param bool $isInitParamsAreArguments
     * @return \app\models\FormObject|\yii\base\Model
     */
    protected function mockForm($loadParams = [], \app\models\BusinessObject $businessObject = null, $initParams = [], $isInitParamsAreArguments = false)
    {
        if ($isInitParamsAreArguments) {
            $params = $businessObject ? array_merge([$businessObject], $initParams) : $initParams;
        } else {
            $params = $businessObject ? [$businessObject, $initParams] : [$initParams];
        }

        /** @var \yii\base\Model|\app\models\FormObject $form */
        $form = Yii::createObject($this->formClass, $params);
        $form->load($loadParams, '');

        return $form;
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
