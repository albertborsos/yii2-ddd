<?php

namespace albertborsos\ddd\tests\support\base;

use albertborsos\ddd\interfaces\FormObject;
use yii\base\Model;

class StubbedForm extends Model implements FormObject
{
    public $email;
}
