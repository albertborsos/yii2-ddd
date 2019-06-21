<?php

namespace albertborsos\ddd\tests\support\base;

use albertborsos\ddd\models\AbstractService;
use yii\base\Model;

class StubbedService extends AbstractService
{
    public function execute()
    {
        $this->setId(1);
        return true;
    }

    public function failedExecute()
    {
        $this->getForm()->addError('email', 'This email address is already in use.');

        return false;
    }

    public function testGetForm()
    {
        return $this->getForm();
    }

    public function testGetModel()
    {
        return $this->getModel();
    }
}
