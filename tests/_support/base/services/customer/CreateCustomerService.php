<?php

namespace albertborsos\ddd\tests\support\base\services\customer;

use albertborsos\ddd\tests\support\base\services\customer\forms\CreateCustomerForm;

class CreateCustomerService extends AbstractCustomerService
{
    public function __construct(CreateCustomerForm $form, $config = [])
    {
        parent::__construct($form, null, $config);
    }
}
