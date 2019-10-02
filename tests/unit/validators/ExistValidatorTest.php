<?php

namespace albertborsos\ddd\tests\unit\validators;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\tests\fixtures\CustomerFixtures;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerRepositoryInterface;
use albertborsos\ddd\tests\support\base\services\customer\forms\CreateCustomerAddressForm;
use albertborsos\ddd\tests\support\base\services\customer\forms\DynamicRulesCustomerForm;
use albertborsos\ddd\validators\ExistValidator;
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

    public function targetAttributeDataProvider()
    {
        return [
            'not set' => [null],
            'as string' => ['name'],
            'as array value only' => [['name']],
            'as array key-value pair' => [['name' => 'name']],
        ];
    }

    /**
     * @dataProvider targetAttributeDataProvider
     * @param $targetAttribute
     * @throws \yii\base\InvalidConfigException
     */
    public function testTargetAttributeFormats($targetAttribute)
    {
        $rules = [
            [['name'], ExistValidator::class, 'targetRepository' => CustomerRepositoryInterface::class, 'targetAttribute' => $targetAttribute],
        ];

        $form = $this->mockForm(DynamicRulesCustomerForm::class, [
            'name' => 'Sándor',
            'rules' => $rules,
        ]);

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('name', $form->getErrors());
        $this->assertCount(1, $form->getErrors());
    }

    /**
     * @param $class
     * @param array $data
     * @return EntityInterface
     * @throws \yii\base\InvalidConfigException
     */
    private function mockForm($class, array $data): EntityInterface
    {
        return \Yii::createObject($class, [$data]);
    }
}
