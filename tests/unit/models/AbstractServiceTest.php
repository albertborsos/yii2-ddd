<?php

namespace albertborsos\ddd\tests\unit\models;

use albertborsos\ddd\tests\support\base\StubbedForm;
use albertborsos\ddd\tests\support\base\StubbedEntity;
use albertborsos\ddd\tests\support\base\StubbedService;
use Codeception\PHPUnit\TestCase;
use yii\base\Model;

class AbstractServiceTest extends TestCase
{
    /**
     * @param null $form
     * @param null $entity
     * @return StubbedService
     */
    public function mockService($form = null, $entity = null)
    {
        if (!empty(func_get_args())) {
            return \Yii::createObject(StubbedService::className(), func_get_args());
        }

        return \Yii::createObject(StubbedService::className());
    }

    public function invalidConstructionDataProvider()
    {
        return [
            'no arguments is valid'    => [[], null],
            'invalid form interface'   => [[new Model()], 'TypeError'],
            'invalid entity interface' => [[new StubbedForm(), new Model()], 'TypeError'],
            'valid arguments'          => [[new StubbedForm(), new StubbedEntity()], null],
        ];
    }

    /**
     * @dataProvider invalidConstructionDataProvider
     *
     * @param $constructorArguments
     * @param $expectedException
     */
    public function testInvalidObjectInitialization($constructorArguments, $expectedException)
    {
        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }
        call_user_func_array([$this, 'mockService'], $constructorArguments);
    }

    public function testGetFormObject()
    {
        $mockedForm = new StubbedForm();
        $service = $this->mockService($mockedForm);

        $this->assertSame($mockedForm, $service->testGetForm());
    }

    public function testGetEntityObject()
    {
        $mockedEntity = new StubbedEntity();
        $service = $this->mockService(null, $mockedEntity);

        $this->assertSame($mockedEntity, $service->testGetEntity());
    }

    public function testExecuteOk()
    {
        $mockedForm = new StubbedForm();
        $mockedModel = new StubbedEntity();

        $service = $this->mockService($mockedForm, $mockedModel);

        $this->assertNull($service->getId());
        $this->assertTrue($service->execute());
        $this->assertNotNull($service->getId());
        $this->assertEmpty($mockedForm->errors);
    }

    public function testExecuteFailed()
    {
        $mockedForm = new StubbedForm();
        $mockedModel = new StubbedEntity();

        $service = $this->mockService($mockedForm, $mockedModel);

        $this->assertEmpty($mockedForm->errors);
        $this->assertNull($service->getId());
        $this->assertFalse($service->failedExecute());
        $this->assertNull($service->getId());
        $this->assertNotEmpty($mockedForm->errors);
    }
}
