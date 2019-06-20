<?php

namespace albertborsos\ddd\tests\support\base;

use albertborsos\ddd\models\AbstractService;
use yii\base\Model;

class MockedService extends AbstractService
{
    public function execute()
    {
        return true;
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
