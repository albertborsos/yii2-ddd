<?php

namespace albertborsos\ddd\tests\support\base;

use albertborsos\ddd\interfaces\RepositoryInterface;
use Yii;

abstract class AbstractServiceTest extends AbstractFormTest
{
    /**
     * @var string
     */
    protected $serviceClass;

    /**
     * @var string
     */
    protected $repositoryInterface;

    /**
     * @param \albertborsos\ddd\interfaces\FormObject $formObject
     * @param \albertborsos\ddd\interfaces\BusinessObject|null $entity
     * @return \albertborsos\ddd\models\AbstractService
     */
    protected function mockService(\albertborsos\ddd\interfaces\FormObject $formObject, \albertborsos\ddd\interfaces\EntityInterface $entity = null)
    {
        return Yii::createObject($this->serviceClass, [$formObject, $entity]);
    }

    /**
     * @return RepositoryInterface
     * @throws \yii\base\InvalidConfigException
     */
    protected function getRepository()
    {
        return Yii::createObject($this->repositoryInterface);
    }
}
