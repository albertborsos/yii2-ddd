<?php

namespace albertborsos\ddd\tests\unit\validators;

use albertborsos\ddd\tests\fixtures\CustomerFixtures;
use albertborsos\ddd\tests\support\base\services\customer\forms\AbstractCustomerAddressForm;
use albertborsos\ddd\tests\support\base\services\customer\forms\CreateCustomerAddressForm;
use Codeception\PHPUnit\TestCase;
use yii\test\FixtureTrait;

class ExistValidatorTest extends TestCase
{
    use FixtureTrait;

    const VALID_ADDRESS_DATA = [
        'customerId' => 1,
        'zipCode' => 2030,
        'city' => 'Érd',
        'street' => 'Hunor utca 2',
    ];

    public function fixtures()
    {
        return [
            'customers' => CustomerFixtures::class,
        ];
    }

    public function testCustomerIdIsExist()
    {
        $form = $this->mockForm(CreateCustomerAddressForm::class, self::VALID_ADDRESS_DATA);

        $this->assertTrue($form->validate());
    }

    public function testCustomerIdIsNotExist()
    {
        $form = $this->mockForm(CreateCustomerAddressForm::class, array_merge(self::VALID_ADDRESS_DATA, [
            'customerId' => 99,
        ]));

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('customerId', $form->getErrors());
        $this->assertCount(1, $form->getErrors());
    }

    /**
     * @param $class
     * @param array $data
     * @return AbstractCustomerAddressForm
     * @throws \yii\base\InvalidConfigException
     */
    private function mockForm($class, array $data): AbstractCustomerAddressForm
    {
        return \Yii::createObject($class, [$data]);
    }
}
