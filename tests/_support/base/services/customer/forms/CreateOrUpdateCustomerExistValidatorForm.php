<?php

namespace albertborsos\ddd\tests\support\base\services\customer\forms;

use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerActiveRepositoryInterface;
use albertborsos\ddd\validators\ExistValidator;

class CreateOrUpdateCustomerExistValidatorForm extends AbstractCustomerForm
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['name'], ExistValidator::class, 'targetRepository' => CustomerActiveRepositoryInterface::class],
        ]);
    }
}
