<?php

namespace albertborsos\ddd\tests\support\base\services\customer;

use albertborsos\ddd\interfaces\ActiveRepositoryInterface;
use albertborsos\ddd\models\AbstractActiveService;
use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerRepositoryInterface;
use albertborsos\ddd\tests\support\base\services\customer\forms\AbstractCustomerForm;

abstract class AbstractCustomerService extends AbstractActiveService
{
    /** @var string|ActiveRepositoryInterface */
    protected $repository = CustomerRepositoryInterface::class;

    public function __construct(AbstractCustomerForm $form = null, Customer $model = null, $config = [])
    {
        parent::__construct($form, $model, $config);
    }
}
