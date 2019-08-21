<?php

namespace albertborsos\ddd\tests\support\base\services\customer\forms;

class CreateCustomerForm extends AbstractCustomerForm
{
    public $customProperty;

    public function rules()
    {
        return array_merge(parent::rules(), [
//            [['customProperty'], 'required'],
        ]);
    }
}
