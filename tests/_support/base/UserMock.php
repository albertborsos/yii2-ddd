<?php

namespace albertborsos\ddd\tests\support\base;

use yii\base\BaseObject;

class UserMock extends BaseObject
{
    public $id;
    public $isGuest = true;

    public function login($id)
    {
        $this->isGuest = false;
        $this->id = $id;
    }
    public function logout()
    {
        $this->isGuest = true;
        $this->id = null;
    }
}
