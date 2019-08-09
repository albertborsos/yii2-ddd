<?php

class MockTraitTest extends \Codeception\PHPUnit\TestCase
{
    use \albertborsos\ddd\tests\support\base\MockTrait;

    public function mockObjectDataProvider()
    {
        return [
            'mock form attributes only' => [\albertborsos\ddd\tests\support\base\StubForm::class, ['email' => 'a@b.hu'], []],
            'mock form attribute and method' => [\albertborsos\ddd\tests\support\base\StubForm::class, ['email' => 'a@b.hu'], ['validate' => true]],
            'mock service execute method' => [\albertborsos\ddd\tests\support\base\StubService::class, [], ['execute' => true]],
            'mock service with multiple settings' => [\albertborsos\ddd\tests\support\base\StubService::class, [], [
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
        $this->testMockConfig($mockConfig, $attributes, $settings);
    }

    /**
     * @dataProvider mockObjectDataProvider
     *
     * @param $mockedClass
     * @param $attributes
     * @param $settings
     */
    public function testToMockObject($class, $attributes, $settings)
    {
        if (!empty($class)) {
            $mockConfig['class'] = $class;
        }
        if (!empty($attributes)) {
            $mockConfig['attributes'] = $attributes;
        }
        if (!empty($settings)) {
            $mockConfig['settings'] = $settings;
        }

        $this->testMockConfig($mockConfig, $attributes, $settings);
    }

    /**
     * @param $attributes
     * @param $settings
     * @param $mockConfig
     */
    protected function testMockConfig($mockConfig, $attributes, $settings): void
    {
        $mockedObject = $this->mockObject($mockConfig);

        foreach ($attributes as $attribute => $expectedValue) {
            $this->assertEquals($expectedValue, $mockedObject->$attribute);
        }

        foreach ($settings as $method => $expectedResult) {
            $this->assertEquals($expectedResult, call_user_func([$mockedObject, $method]));
        }
    }
}
