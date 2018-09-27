<?php

namespace albertborsos\ddd\tests\support\base;

use Yii;

/**
 * Class AbstractDomainTest
 * @deprecated since 0.3.0, will be removed in 1.0.0. Use `albertborsos\ddd\tests\support\base\AbstractServiceTest` instead.
 * @package albertborsos\ddd\tests\support\base
 */
abstract class AbstractDomainTest extends Unit
{
    protected $formClass;
    protected $domainClass;
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
     * @return \albertborsos\ddd\models\AbstractDomain
     */
    protected function mockDomain(\albertborsos\ddd\interfaces\FormObject $formObject, \albertborsos\ddd\interfaces\BusinessObject $businessObject = null)
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
