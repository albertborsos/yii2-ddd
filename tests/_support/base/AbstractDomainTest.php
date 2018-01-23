<?php

namespace albertborsos\ddd\tests\support\base;

use Yii;

abstract class AbstractDomainTest extends \yii\codeception\DbTestCase
{
    protected $formClass;
    protected $domainClass;
    protected $modelClass;

    /**
     * @param array $loadParams
     * @param \app\models\BusinessObject|null $businessObject
     * @param array $initParams
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
     * @param \app\models\FormObject $formObject
     * @param \app\models\BusinessObject|null $businessObject
     * @return \app\models\AbstractDomain
     */
    protected function mockDomain(\app\models\FormObject $formObject, \app\models\BusinessObject $businessObject = null)
    {
        return Yii::createObject($this->domainClass, [$formObject, $businessObject]);
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
