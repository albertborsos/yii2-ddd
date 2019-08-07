<?php

namespace albertborsos\ddd\tests\support\base;

use albertborsos\ddd\interfaces\EntityInterface;
use Yii;

abstract class AbstractFormTest extends Unit
{
    /**
     * @var string
     */
    protected $formClass;

    /**
     * @var string
     * @since 2.0.0
     */
    protected $entityClass;

    /**
     * @param array $loadParams
     * @param EntityInterface|null $entity
     * @param array $initParams
     * @param bool $isInitParamsAreArguments
     * @return \albertborsos\ddd\interfaces\FormObject|\yii\base\Model
     * @throws \yii\base\InvalidConfigException
     */
    protected function mockForm($loadParams = [], \albertborsos\ddd\interfaces\EntityInterface $entity = null, $initParams = [], $isInitParamsAreArguments = false)
    {
        if ($isInitParamsAreArguments) {
            $params = $entity ? array_merge([$entity], $initParams) : $initParams;
        } else {
            $params = $entity ? [$entity, $initParams] : [$initParams];
        }

        /** @var \yii\base\Model|\albertborsos\ddd\interfaces\FormObject $form */
        $form = Yii::createObject($this->formClass, $params);
        $form->load($loadParams, '');

        return $form;
    }

    /**
     * @param $config
     * @return EntityInterface
     * @throws \yii\base\InvalidConfigException
     */
    protected function mockEntity($config)
    {
        $entityClass = $this->entityClass ?? $this->modelClass;

        return Yii::createObject($entityClass, [$config]);
    }
}
