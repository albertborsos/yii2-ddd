<?php

namespace albertborsos\ddd\tests\support\base\services\customer;

use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use albertborsos\ddd\tests\support\base\services\customer\forms\UpdateCustomerForm;

class UpdateCustomerService extends AbstractCustomerService
{
    public function __construct(UpdateCustomerForm $form, Customer $model, $config = [])
    {
        parent::__construct($form, $model, $config);
    }
}
