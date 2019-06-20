<?php

namespace albertborsos\ddd\tests\unit\models;

use albertborsos\ddd\models\AbstractService;
use albertborsos\ddd\tests\support\base\MockedForm;
use albertborsos\ddd\tests\support\base\MockedModel;
use albertborsos\ddd\tests\support\base\MockedService;
use Codeception\PHPUnit\TestCase;
use Codeception\Util\Debug;
use PHPUnit\Framework\MockObject\MockObject;
use yii\base\Model;
use yii\helpers\Json;
use yii\helpers\VarDumper;

class AbstractServiceTest extends TestCase
{
    /**
     * @param null $form
     * @param null $model
     * @return MockedService
     */
    public function mockService($form = null, $model = null)
    {
        if (!empty(func_get_args())) {
            return \Yii::createObject(MockedService::className(), func_get_args());
        }

        return \Yii::createObject(MockedService::className());
    }

    public function invalidConstructionDataProvider()
    {
        return [
            'no arguments is valid'   => [[], null],
            'invalid form interface'  => [[new Model()], 'TypeError'],
            'invalid model interface' => [[new MockedForm(), new Model()], 'TypeError'],
            'valid arguments'         => [[new MockedForm(), new MockedModel()], null],
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
        $mockedForm = new MockedForm();
        $service = $this->mockService($mockedForm);

        $this->assertSame($mockedForm, $service->testGetForm());
    }

    public function testGetModelObject()
    {
        $mockedModel = new MockedModel();
        $service = $this->mockService(null, $mockedModel);

        $this->assertSame($mockedModel, $service->testGetModel());
    }
}
