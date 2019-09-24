<?php

namespace albertborsos\ddd\tests\support\base\services\customer;

use albertborsos\ddd\interfaces\RepositoryInterface;
use albertborsos\ddd\models\AbstractStoreService;
use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerRepositoryInterface;
use albertborsos\ddd\tests\support\base\services\customer\forms\AbstractCustomerForm;

abstract class AbstractCustomerService extends AbstractStoreService
{
    /** @var string|RepositoryInterface */
    protected $repository = CustomerRepositoryInterface::class;

    public function __construct(AbstractCustomerForm $form = null, Customer $model = null, $config = [])
    {
        parent::__construct($form, $model, $config);
    }
}
