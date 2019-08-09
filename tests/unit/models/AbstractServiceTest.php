<?php

namespace albertborsos\ddd\tests\unit\models;

use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use albertborsos\ddd\tests\support\base\services\customer\CreateCustomerService;
use albertborsos\ddd\tests\support\base\services\customer\forms\CreateCustomerForm;
use albertborsos\ddd\tests\support\base\services\customer\forms\UpdateCustomerForm;
use albertborsos\ddd\tests\support\base\services\customer\UpdateCustomerService;
use albertborsos\ddd\tests\support\base\StubForm;
use albertborsos\ddd\tests\support\base\StubEntity;
use albertborsos\ddd\tests\support\base\StubService;
use Codeception\PHPUnit\TestCase;
use Codeception\Util\Debug;
use yii\base\Model;

class AbstractServiceTest extends TestCase
{
    /**
     * @param $serviceClass
     * @param null $form
     * @param null $entity
     * @return UpdateCustomerService|object
     * @throws \yii\base\InvalidConfigException
     */
    public function mockService($serviceClass, $args)
    {
        if (!empty($args)) {
            return \Yii::createObject($serviceClass, $args);
        }

        return \Yii::createObject($serviceClass);
    }

    public function constructionDataProvider()
    {
        return [
            'no arguments is valid'          => [StubService::class, [], null],
            'invalid form interface (model)' => [CreateCustomerService::class, [new Model()], 'TypeError'],
            'invalid form interface (form)'  => [CreateCustomerService::class, [new UpdateCustomerForm()], 'TypeError'],
            'invalid entity interface'       => [UpdateCustomerService::class, [new UpdateCustomerForm(), new Model()], 'TypeError'],
            'valid arguments'                => [UpdateCustomerService::class, [new UpdateCustomerForm(), new Customer()], null],
        ];
    }

    /**
     * @dataProvider constructionDataProvider
     *
     * @param $constructorArguments
     * @param $expectedException
     */
    public function testInvalidObjectInitialization($serviceClass, $constructorArguments, $expectedException)
    {
        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }
        call_user_func_array([$this, 'mockService'], [$serviceClass, $constructorArguments]);
    }

    public function testGetFormObject()
    {
        $mockedForm = new StubForm();
        $service = $this->mockService(StubService::class, [$mockedForm]);

        $this->assertSame($mockedForm, $service->testGetForm());
    }

    public function testGetEntityObject()
    {
        $mockedEntity = new StubEntity();
        $service = $this->mockService(StubService::class, [null, $mockedEntity]);

        $this->assertSame($mockedEntity, $service->testGetEntity());
    }

    public function testExecuteOk()
    {
        $mockedForm = new StubForm();
        $mockedModel = new StubEntity();

        $service = $this->mockService(StubService::class, [$mockedForm, $mockedModel]);

        $this->assertNull($service->getId());
        $this->assertTrue($service->execute());
        $this->assertNotNull($service->getId());
        $this->assertEmpty($mockedForm->errors);
    }

    public function testExecuteFailed()
    {
        $mockedForm = new StubForm();
        $mockedModel = new StubEntity();

        $service = $this->mockService(StubService::class, [$mockedForm, $mockedModel]);

        $this->assertEmpty($mockedForm->errors);
        $this->assertNull($service->getId());
        $this->assertFalse($service->failedExecute());
        $this->assertNull($service->getId());
        $this->assertNotEmpty($mockedForm->errors);
    }
}
