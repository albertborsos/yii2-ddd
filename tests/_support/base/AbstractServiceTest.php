<?php

namespace albertborsos\ddd\tests\support\base;

use Yii;

abstract class AbstractServiceTest extends AbstractFormTest
{
    /**
     * @var string
     */
    protected $serviceClass;

    /**
     * @param \albertborsos\ddd\interfaces\FormObject $formObject
     * @param \albertborsos\ddd\interfaces\BusinessObject|null $entity
     * @return \albertborsos\ddd\models\AbstractService
     */
    protected function mockService(\albertborsos\ddd\interfaces\FormObject $formObject, \albertborsos\ddd\interfaces\EntityInterface $entity = null)
    {
        return Yii::createObject($this->serviceClass, [$formObject, $entity]);
    }
}
