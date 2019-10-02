<?php

namespace albertborsos\ddd\tests\support\base\services\customer\forms;

class DynamicRulesCustomerForm extends AbstractCustomerForm
{
    public $rules;

    public function rules()
    {
        return array_merge(parent::rules(), $this->rules);
    }
}
