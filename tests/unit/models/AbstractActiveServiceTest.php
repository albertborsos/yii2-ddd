<?php

namespace albertborsos\ddd\tests\unit\models;

use albertborsos\ddd\tests\fixtures\CustomerFixtures;
use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerRepositoryInterface;
use albertborsos\ddd\tests\support\base\services\customer\CreateCustomerService;
use albertborsos\ddd\tests\support\base\services\customer\forms\CreateCustomerForm;
use Codeception\PHPUnit\TestCase;

class AbstractActiveServiceTest extends TestCase
{
    public function testExecute()
    {
        $form = new CreateCustomerForm(['name' => 'Active Customer Service Test']);
        $service = new CreateCustomerService($form);

        $this->assertTrue($service->execute());
        $this->assertNotNull($service->getId());

        $repository = \Yii::createObject(CustomerRepositoryInterface::class);
        /** @var Customer $model */
        $model = $repository->findById($service->getId());

        $this->assertInstanceOf(Customer::class, $model);
        $this->assertEquals('Active Customer Service Test', $model->name);
    }
}
