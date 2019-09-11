<?php

namespace albertborsos\ddd\tests\support\base\services\customer\forms;

use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerRepositoryInterface;
use albertborsos\ddd\validators\UniqueValidator;

class CreateOrUpdateCustomerUniqueValidatorForm extends AbstractCustomerForm
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name'], UniqueValidator::class, 'targetRepository' => CustomerRepositoryInterface::class],
        ]);
    }
}
