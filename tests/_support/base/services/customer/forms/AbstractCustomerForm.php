<?php

namespace albertborsos\ddd\tests\support\base\services\customer\forms;

use albertborsos\ddd\interfaces\FormObject;
use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;

abstract class AbstractCustomerForm extends Customer implements FormObject
{
    public function rules()
    {
        return [
            [['name'], 'trim'],
            [['name'], 'default'],
            [['name'], 'required'],

            [['name'], 'string', 'max' => 255],
        ];
    }
}
