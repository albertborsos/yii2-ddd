<?php

namespace albertborsos\ddd\tests\unit\validators;

use albertborsos\ddd\tests\fixtures\CustomerFixtures;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerRepositoryInterface;
use albertborsos\ddd\tests\support\base\services\customer\forms\AbstractCustomerForm;
use albertborsos\ddd\tests\support\base\services\customer\forms\DynamicRulesCustomerForm;
use albertborsos\ddd\validators\UniqueValidator;
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
     */
    public function testNewEntityIsNotUnique($targetAttribute)
    {
        $form = $this->mockForm([
            'name' => 'Albert',
            'rules' => $this->mockRules($targetAttribute),
        ]);

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('name', $form->getErrors());
        $this->assertCount(1, $form->getErrors());
    }

    /**
     * @dataProvider targetAttributeDataProvider
     */
    public function testNewEntityIsUnique($targetAttribute)
    {
        $form = $this->mockForm([
            'name' => 'Sándor',
            'rules' => $this->mockRules($targetAttribute),
        ]);

        $this->assertTrue($form->validate());
    }

    /**
     * @dataProvider targetAttributeDataProvider
     */
    public function testUpdateEntityWithoutModificationIsValid($targetAttribute)
    {
        $form = $this->mockForm([
            'id' => 1,
            'name' => 'Albert',
            'rules' => $this->mockRules($targetAttribute),
        ]);

        $this->assertTrue($form->validate());
    }

    /**
     * @dataProvider targetAttributeDataProvider
     */
    public function testUpdateEntityForUniqueName($targetAttribute)
    {
        $form = $this->mockForm([
            'id' => 1,
            'name' => 'Sándor',
            'rules' => $this->mockRules($targetAttribute),
        ]);

        $this->assertTrue($form->validate());
    }

    /**
     * @dataProvider targetAttributeDataProvider
     */
    public function testUpdateEntityForNotUniqueName($targetAttribute)
    {
        $form = $this->mockForm([
            'id' => 1,
            'name' => 'Noncsi',
            'rules' => $this->mockRules($targetAttribute),
        ]);

        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('name', $form->getErrors());
        $this->assertCount(1, $form->getErrors());
    }

    protected function mockForm(array $data): AbstractCustomerForm
    {
        $data['rules'] = $data['rules'] ?? $this->mockRules(null);

        $form = \Yii::createObject(DynamicRulesCustomerForm::class);
        $form->setAttributes($data, false);

        return $form;
    }

    /**
     * @param $targetAttribute
     * @return array
     */
    protected function mockRules($targetAttribute): array
    {
        return [
            [['name'], UniqueValidator::class, 'targetRepository' => CustomerRepositoryInterface::class, 'targetAttribute' => $targetAttribute],
        ];
    }
}
