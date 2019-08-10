<?php

namespace albertborsos\ddd\tests\support\base;

use albertborsos\ddd\models\AbstractService;
use yii\base\Model;

class StubService extends AbstractService
{
    public function execute(): bool
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

    public function testGetEntity()
    {
        return $this->getEntity();
    }
}
