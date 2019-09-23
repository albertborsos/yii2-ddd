<?php

namespace albertborsos\ddd\tests\unit\validators;

use albertborsos\ddd\tests\fixtures\CustomerFixtures;
use albertborsos\ddd\tests\support\base\services\customer\forms\CreateOrUpdateCustomerUniqueValidatorForm;
use Codeception\PHPUnit\TestCase;
use yii\test\FixtureTrait;

class UniqueValidatorTest extends TestCase
{
    use FixtureTrait;

    public function fixtures()
    {
        return [
            'customers' => CustomerFixtures::class,
        ];
    }

    public function testNewEntityIsNotUnique()
    {
        $form = $this->mockForm(['name' => 'Albert']);

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('name', $form->getErrors());
        $this->assertCount(1, $form->getErrors());
    }

    public function testNewEntityIsUnique()
    {
        $form = $this->mockForm(['name' => 'Sándor']);

        $this->assertTrue($form->validate());
    }

    public function testUpdateEntityWithoutModificationIsValid()
    {
        $form = $this->mockForm(['id' => 1, 'name' => 'Albert']);

        $this->assertTrue($form->validate());
    }

    public function testUpdateEntityForUniqueName()
    {
        $form = $this->mockForm(['id' => 1, 'name' => 'Sándor']);

        $this->assertTrue($form->validate());
    }

    public function testUpdateEntityForNotUniqueName()
    {
        $form = $this->mockForm(['id' => 1, 'name' => 'Noncsi']);

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('name', $form->getErrors());
        $this->assertCount(1, $form->getErrors());
    }

    private function mockForm(array $data): CreateOrUpdateCustomerUniqueValidatorForm
    {
        $form = \Yii::createObject(CreateOrUpdateCustomerUniqueValidatorForm::class);
        $form->setAttributes($data, false);

        return $form;
    }
}
