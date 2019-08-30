<?php

namespace albertborsos\ddd\tests\unit\models;

use albertborsos\ddd\tests\fixtures\CustomerFixtures;
use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use albertborsos\ddd\tests\support\base\domains\customer\interfaces\CustomerActiveRepositoryInterface;
use albertborsos\ddd\tests\support\base\services\customer\CreateCustomerService;
use albertborsos\ddd\tests\support\base\services\customer\forms\CreateCustomerForm;
use Codeception\PHPUnit\TestCase;
use Codeception\Util\Debug;
use yii\test\FixtureTrait;

class AbstractActiveServiceTest extends TestCase
{
    public function testExecute()
    {
        $form = new CreateCustomerForm(['name' => 'Active Customer Service Test']);
        $service = new CreateCustomerService($form);

        $this->assertTrue($service->execute());
        $this->assertNotNull($service->getId());

        $repository = \Yii::createObject(CustomerActiveRepositoryInterface::class);
        /** @var Customer $model */
        $model = $repository->findById($service->getId());

        $this->assertInstanceOf(Customer::class, $model);
        $this->assertEquals('Active Customer Service Test', $model->name);
    }

    public function testExecuteButModelValidationFails()
    {
        $longerThan255Character = \Yii::$app->security->generateRandomString(256);
        $form = new CreateCustomerForm(['name' => $longerThan255Character]);
        $service = new CreateCustomerService($form);

        $this->assertFalse($service->execute());
        $this->assertNull($service->getId());
        $this->assertCount(1, $form->getErrors());
        $this->assertArrayHasKey('name', $form->getErrors());
    }
}
