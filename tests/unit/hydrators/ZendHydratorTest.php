<?php

namespace albertborsos\ddd\tests\unit\hydrators;

use albertborsos\ddd\hydrators\ZendHydrator;
use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerAddress;
use albertborsos\ddd\tests\support\base\MockTrait;
use albertborsos\ddd\tests\support\base\Customer as CustomerModel;
use Codeception\PHPUnit\TestCase;
use yii\base\DynamicModel;
use yii\base\Model;

class ZendHydratorTest extends TestCase
{
    use MockTrait;

    public function dataProviderHydrateSingleEntity()
    {
        return [
            'data array' => [Customer::class, ['id' => 1, 'name' => 'Name']],
            'model' => [Customer::class, new DynamicModel(['id' => 1, 'name' => 'Name'])],
        ];
    }

    public function dataProviderHydrateIntoObject()
    {
        return [
            'data array' => [Customer::class, ['id' => 1, 'name' => 'Name']],
            'data array with relation' => [Customer::class, ['id' => 1, 'name' => 'Name', 'customerAddresses' => [
                ['id' => 1, 'customer_id' => 1, 'zip_code' => 2030, 'city' => 'Érd', 'street' => 'Balatoni út 51.'],
            ]]],
        ];
    }

    /**
     * @dataProvider dataProviderHydrateSingleEntity
     */
    public function testHydrateSingleEntity($entityClass, $data)
    {
        $hydrator = $this->mockHydrator();

        /** @var EntityInterface $entity */
        $entity = $hydrator->hydrate($entityClass, $data);

        $this->assertHydratedEntity($entityClass, $entity, $data);
    }

    public function dataProviderHydrateSingleModel()
    {
        return [
            'data array' => [CustomerModel::class, ['id' => 1, 'name' => 'Name']],
            'model'      => [CustomerModel::class, new DynamicModel(['id' => 1, 'name' => 'Name'])],
        ];
    }

    /**
     * @dataProvider dataProviderHydrateSingleModel
     */
    public function testHydrateSingleModel($modelClass, $data)
    {
        $hydrator = $this->mockHydrator();

        /** @var Model $model */
        $model = $hydrator->hydrate($modelClass, $data);

        $this->assertHydratedModel($modelClass, $data, $model);
    }

    public function dataProviderHydrateMultipleModels()
    {
        return [
            'data array' => [CustomerModel::class, [
                ['id' => 1, 'name' => 'Name'],
                ['id' => 2, 'name' => 'Name'],
                ['id' => 3, 'name' => 'Name'],
            ]],
            'model' => [CustomerModel::class, [
                new DynamicModel(['id' => 1, 'name' => 'Name']),
                new DynamicModel(['id' => 2, 'name' => 'Name']),
                new DynamicModel(['id' => 3, 'name' => 'Name']),
            ]],
        ];
    }

    /**
     * @dataProvider dataProviderHydrateMultipleModels
     */
    public function testHydrateMultipleModels($modelClass, $data)
    {
        $hydrator = $this->mockHydrator();

        $models = $hydrator->hydrateAll($modelClass, $data);

        foreach ($data as $i => $modelData) {
            $this->assertHydratedModel($modelClass, $modelData, $models[$i]);
        }
    }

    public function dataProviderHydrateMultipleEntities()
    {
        return [
            'data array' => [Customer::class, [
                ['id' => 1, 'name' => 'Name'],
                ['id' => 2, 'name' => 'Name'],
                ['id' => 3, 'name' => 'Name'],
            ]],
            'model' => [Customer::class, [
                new DynamicModel(['id' => 1, 'name' => 'Name']),
                new DynamicModel(['id' => 2, 'name' => 'Name']),
                new DynamicModel(['id' => 3, 'name' => 'Name']),
            ]],
        ];
    }

    /**
     * @dataProvider dataProviderHydrateMultipleEntities
     *
     * @param $entityClass
     * @param $models
     * @return void
     */
    public function testHydrateMultipleEntities($entityClass, $models)
    {
        $hydrator = $this->mockHydrator();

        /** @var EntityInterface $entity */
        $entities = $hydrator->hydrateAll($entityClass, $models);

        foreach ($entities as $i => $entity) {
            $this->assertHydratedEntity($entityClass, $entity, $models[$i]);
        }
    }

    /**
     * @dataProvider dataProviderHydrateIntoObject
     *
     * @param $entityClass
     * @param $data
     * @throws \yii\base\InvalidConfigException
     */
    public function testHydrateInto($entityClass, $data)
    {
        $hydrator = $this->mockHydrator();

        $model = \Yii::createObject($entityClass);
        foreach ($data as $attribute => $value) {
            $this->assertNotEquals($value, $model->$attribute);
        }

        /** @var EntityInterface $entity */
        $entity = $hydrator->hydrateInto($model, $data);

        foreach ($data as $attribute => $value) {
            $this->assertEquals($value, $entity->$attribute);
        }
    }

    public function dataProviderExtractHydratedModel()
    {
        return [
            'customer' => [Customer::class, ['id' => 1, 'name' => 'Name', 'customerAddresses' => null, 'status' => null]],
            'customerAddress' => [CustomerAddress::class, ['id' => 1, 'customerId' => 1, 'zipCode' => 2030, 'city' => 'Érd', 'street' => 'Balatoni út 51.']],
        ];
    }

    /**
     * @dataProvider dataProviderExtractHydratedModel
     * @param $entityClass
     * @param $data
     * @throws \yii\base\InvalidConfigException
     */
    public function testExtractHydratedModel($entityClass, $data)
    {
        $hydrator = $this->mockHydrator();

        $entity = $hydrator->hydrate($entityClass, $data);

        $this->assertEquals($data, $hydrator->extract($entity));
    }

    /**
     * @return ZendHydrator
     */
    protected function mockHydrator()
    {
        return new ZendHydrator();
    }

    /**
     * @param $entityClass
     * @param EntityInterface $entity
     * @param $data
     */
    protected function assertHydratedEntity($entityClass, EntityInterface $entity, $data): void
    {
        $this->assertInstanceOf($entityClass, $entity);

        $attributes = $data instanceof Model ? $data->attributes : $data;

        foreach ($attributes as $attribute => $expectedValue) {
            $this->assertEquals($expectedValue, $entity->$attribute);
        }
    }

    /**
     * @param $modelClass
     * @param $data
     * @param Model $model
     */
    private function assertHydratedModel($modelClass, $data, Model $model): void
    {
        $this->assertInstanceOf($modelClass, $model);

        $attributes = $data instanceof Model ? $data->attributes : $data;

        foreach ($attributes as $attribute => $expectedValue) {
            $this->assertEquals($expectedValue, $model->$attribute);
        }
    }


    public function dataProviderHydrateEmptyEntity()
    {
        return [
            'data array' => [Customer::class, []],
            'model' => [Customer::class, new DynamicModel([])],
        ];
    }

    /**
     * @dataProvider dataProviderHydrateEmptyEntity
     * @param $entityClass
     * @param $data
     * @throws \yii\base\InvalidConfigException
     */
    public function testToCreateNewEmptyEntity($entityClass, $data)
    {
        $hydrator = $this->mockHydrator();

        /** @var EntityInterface $entity */
        $entity = $hydrator->hydrate($entityClass, $data);

        $this->assertHydratedEntity($entityClass, $entity, $data);
    }
}
