<?php

namespace albertborsos\ddd\tests\support\base\services\customer\forms;

use yii\base\Model;

class InvalidCreateCustomerForm extends CreateCustomerForm
{
    protected $repository = Model::class;
}
