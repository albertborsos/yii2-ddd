<?php

namespace albertborsos\ddd\tests\support\base;

use albertborsos\ddd\models\AbstractService;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerRepositoryInterface;

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

    public function testGetRepository()
    {
        return $this->getRepository();
    }
}
