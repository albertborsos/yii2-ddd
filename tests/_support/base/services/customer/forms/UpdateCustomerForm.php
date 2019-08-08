<?php

namespace albertborsos\ddd\tests\support\base\services\customer\forms;

class UpdateCustomerForm extends AbstractCustomerForm
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            // [['name'], 'required'],
        ]);
    }
}
