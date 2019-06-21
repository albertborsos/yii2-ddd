<?php

class MockConfigTest extends \Codeception\PHPUnit\TestCase
{
    use \albertborsos\ddd\tests\support\base\MockTrait;

    public function mockConfigDataProvider()
    {
        return [
            [\albertborsos\ddd\tests\support\base\StubbedForm::class, ['email' => 'a@b.hu'], ['validate' => true]],
        ];
    }

    /**
     * @dataProvider mockConfigDataProvider
     *
     * @param $mockedClass
     * @param $attributes
     * @param $settings
     */
    public function testCreateConfig($mockedClass, $attributes, $settings)
    {
        $mockConfig = \albertborsos\ddd\tests\support\base\MockConfig::create($mockedClass, $attributes, $settings);

        $this->assertEquals($mockedClass, $mockConfig['class']);
        $this->assertEquals($attributes, $mockConfig['attributes']);
        $this->assertEquals($settings, $mockConfig['settings']);
    }
}
