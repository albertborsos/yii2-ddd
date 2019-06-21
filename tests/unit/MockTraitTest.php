<?php

class MockTraitTest extends \Codeception\PHPUnit\TestCase
{
    use \albertborsos\ddd\tests\support\base\MockTrait;

    public function mockObjectDataProvider()
    {
        return [
            'mock form attribute and method' => [\albertborsos\ddd\tests\support\base\StubbedForm::class, ['email' => 'a@b.hu'], ['validate' => true]],
            'mock service execute method' => [\albertborsos\ddd\tests\support\base\StubbedService::class, [], ['execute' => true]],
            'mock service with multiple settings' => [\albertborsos\ddd\tests\support\base\StubbedService::class, [], [
                'execute' => true,
                'failedExecute' => false,
            ]],
        ];
    }

    /**
     * @dataProvider mockObjectDataProvider
     *
     * @param $mockedClass
     * @param $attributes
     * @param $settings
     */
    public function testCreateMock($mockedClass, $attributes, $settings)
    {
        $mockConfig = \albertborsos\ddd\tests\support\base\MockConfig::create($mockedClass, $attributes, $settings);
        $mockedObject = $this->mockObject($mockConfig);

        foreach ($attributes as $attribute => $expectedValue) {
            $this->assertEquals($expectedValue, $mockedObject->$attribute);
        }

        foreach ($settings as $method => $expectedResult) {
            $this->assertEquals($expectedResult, call_user_func([$mockedObject, $method]));
        }
    }
}
