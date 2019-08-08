<?php

namespace albertborsos\ddd\tests\unit\data;

use albertborsos\ddd\data\ActiveEntityDataProvider;
use albertborsos\ddd\interfaces\HydratorInterface;
use albertborsos\ddd\tests\fixtures\CustomerFixtures;
use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use PHPUnit\Framework\TestCase;
use yii\base\DynamicModel;
use yii\test\FixtureTrait;

class ActiveEntityDataProviderTest extends TestCase
{
    use FixtureTrait;

    public function fixtures()
    {
        return [
            'customers' => CustomerFixtures::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->initFixtures();
    }

    public function testReturnsEntities()
    {
        $dataProvider = new ActiveEntityDataProvider([
            'entityClass' => Customer::class,
            'hydrator' => \Yii::createObject(HydratorInterface::class, [\Yii::createObject([new Customer(), 'fieldMapping'])]),
            'query' => \albertborsos\ddd\tests\support\base\domains\customer\mysql\Customer::find(),
        ]);

        foreach ($dataProvider->getModels() as $model) {
            $this->assertInstanceOf(Customer::class, $model);
        }
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     */
    public function testThrowsErrorIfEntityClassIsMissing()
    {
        $dataProvider = new ActiveEntityDataProvider([
            'entityClass' => null,
            'hydrator' => \Yii::createObject(HydratorInterface::class, [\Yii::createObject([new Customer(), 'fieldMapping'])]),
            'query' => \albertborsos\ddd\tests\support\base\domains\customer\mysql\Customer::find(),
        ]);
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     */
    public function testThrowsErrorIfEntityClassNotInstanceOfEntityInterface()
    {
        $dataProvider = new ActiveEntityDataProvider([
            'entityClass' => \albertborsos\ddd\tests\support\base\domains\customer\mysql\Customer::class,
            'hydrator' => \Yii::createObject(HydratorInterface::class, [\Yii::createObject([new Customer(), 'fieldMapping'])]),
            'query' => \albertborsos\ddd\tests\support\base\domains\customer\mysql\Customer::find(),
        ]);
    }

    public function dataProviderInvalidHydrator()
    {
        return [
            'hydrator is missing (null)' => [null],
            'hydrator is missing (empty string)' => [''],
            'hydrator is not instance of HydratorInterface' => [new \yii\base\Model()],
        ];
    }

    /**
     * @dataProvider dataProviderInvalidHydrator
     * @expectedException \yii\base\InvalidConfigException
     */
    public function testThrowsErrorIfHydratorIsMissing($hydrator)
    {
        $dataProvider = new ActiveEntityDataProvider([
            'entityClass' => Customer::class,
            'hydrator' => $hydrator,
            'query' => \albertborsos\ddd\tests\support\base\domains\customer\mysql\Customer::find(),
        ]);
    }
}
