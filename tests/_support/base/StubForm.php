<?php

namespace albertborsos\ddd\tests\support\base;

use albertborsos\ddd\interfaces\FormObject;
use yii\base\Model;

class StubForm extends Model implements FormObject
{
    public $email;
}
