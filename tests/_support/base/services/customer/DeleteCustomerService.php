<?php

namespace albertborsos\ddd\tests\support\base\services\customer;

use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use albertborsos\ddd\tests\support\base\services\customer\forms\DeleteCustomerForm;

class DeleteCustomerService extends AbstractCustomerService
{
    public function __construct(DeleteCustomerForm $form, Customer $model, $config = [])
    {
        parent::__construct($form, $model, $config);
    }

    /**
     * @return bool
     */
    public function execute(): bool
    {
        return $this->getRepository()->delete($this->getEntity());
    }
}
