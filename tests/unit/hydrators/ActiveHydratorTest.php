<?php

namespace albertborsos\ddd\tests\unit\hydrators;

use albertborsos\ddd\hydrators\ActiveHydrator;
use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerAddress;
use albertborsos\ddd\tests\support\base\MockTrait;
use albertborsos\ddd\tests\support\base\Customer as CustomerModel;
use Codeception\PHPUnit\TestCase;
use Codeception\Util\Debug;
use yii\base\DynamicModel;
use yii\base\Model;

class ActiveHydratorTest extends TestCase
{
    use MockTrait;

    public function dataProviderHydrateSingleEntity()
    {
        return [
            'data array' => [Customer::class, ['id' => 'id', 'name' => 'name', 'customerAddresses' => 'customerAddresses'], ['id' => 1, 'name' => 'Name']],
            'model' => [Customer::class, ['id' => 'id', 'name' => 'name', 'customerAddresses' => 'customerAddresses'], new DynamicModel(['id' => 1, 'name' => 'Name'])],
            'model with relation data' => [Customer::class, ['id' => 'id', 'name' => 'name', 'customerAddresses' => 'customerAddresses'], new DynamicModel(['id' => 1, 'name' => 'Name', 'customerAddresses' => [
                ['id' => 1, 'customer_id' => 1, 'zip_code' => 2030, 'city' => 'Érd', 'street' => 'Balatoni út 51.'],
            ]])],
            'model with relation model' => [Customer::class, ['id' => 'id', 'name' => 'name', 'customerAddresses' => 'customerAddresses'], new DynamicModel(['id' => 1, 'name' => 'Name', 'customerAddresses' => [
                new DynamicModel(['id' => 1, 'customer_id' => 1, 'zip_code' => 2030, 'city' => 'Érd', 'street' => 'Balatoni út 51.']),
            ]])],
        ];
    }

    public function dataProviderHydrateIntoObject()
    {
        return [
            'data array' => [Customer::class, ['id' => 'id', 'name' => 'name', 'customerAddresses' => 'customerAddresses'], ['id' => 1, 'name' => 'Name']],
            'data array with relation' => [Customer::class, ['id' => 'id', 'name' => 'name', 'customerAddresses' => 'customerAddresses'], ['id' => 1, 'name' => 'Name', 'customerAddresses' => [
                ['id' => 1, 'customer_id' => 1, 'zip_code' => 2030, 'city' => 'Érd', 'street' => 'Balatoni út 51.'],
            ]]],
        ];
    }

    /**
     * @dataProvider dataProviderHydrateSingleEntity
     */
    public function testHydrateSingleEntity($entityClass, $map, $data)
    {
        $hydrator = $this->mockHydrator($map);

        /** @var EntityInterface $entity */
        $entity = $hydrator->hydrate($entityClass, $data);

        $this->assertHydratedEntity($entityClass, $entity, $data);
    }

    public function dataProviderHydrateSingleModel()
    {
        return [
            'data array' => [CustomerModel::class, ['id' => 'id', 'name' => 'name'], ['id' => 1, 'name' => 'Name']],
            'model'      => [CustomerModel::class, ['id' => 'id', 'name' => 'name'], new DynamicModel(['id' => 1, 'name' => 'Name'])],
        ];
    }

    /**
     * @dataProvider dataProviderHydrateSingleModel
     */
    public function testHydrateSingleModel($modelClass, $map, $data)
    {
        $hydrator = $this->mockHydrator($map);

        /** @var Model $model */
        $model = $hydrator->hydrate($modelClass, $data);

        $this->assertHydratedModel($modelClass, $data, $model);
    }

    public function dataProviderHydrateMultipleModels()
    {
        return [
            'data array' => [CustomerModel::class, ['id' => 'id', 'name' => 'name'], [
                ['id' => 1, 'name' => 'Name'],
                ['id' => 2, 'name' => 'Name'],
                ['id' => 3, 'name' => 'Name'],
            ]],
            'model' => [CustomerModel::class, ['id' => 'id', 'name' => 'name'], [
                new DynamicModel(['id' => 1, 'name' => 'Name']),
                new DynamicModel(['id' => 2, 'name' => 'Name']),
                new DynamicModel(['id' => 3, 'name' => 'Name']),
            ]],
        ];
    }

    /**
     * @dataProvider dataProviderHydrateMultipleModels
     */
    public function testHydrateMultipleModels($modelClass, $map, $data)
    {
        $hydrator = $this->mockHydrator($map);

        $models = $hydrator->hydrateAll($modelClass, $data);

        foreach ($data as $i => $modelData) {
            $this->assertHydratedModel($modelClass, $modelData, $models[$i]);
        }
    }

    public function dataProviderHydrateMultipleEntities()
    {
        return [
            'data array' => [Customer::class, ['id' => 'id', 'name' => 'name', 'customerAddresses' => 'customerAddresses'], [
                ['id' => 1, 'name' => 'Name'],
                ['id' => 2, 'name' => 'Name'],
                ['id' => 3, 'name' => 'Name'],
            ]],
            'model' => [Customer::class, ['id' => 'id', 'name' => 'name', 'customerAddresses' => 'customerAddresses'], [
                new DynamicModel(['id' => 1, 'name' => 'Name']),
                new DynamicModel(['id' => 2, 'name' => 'Name']),
                new DynamicModel(['id' => 3, 'name' => 'Name']),
            ]],
            'model with relation data' => [Customer::class, ['id' => 'id', 'name' => 'name', 'customerAddresses' => 'customerAddresses'], [
                new DynamicModel(['id' => 1, 'name' => 'Name', 'customerAddresses' => [
                    ['id' => 1, 'customer_id' => 1, 'zip_code' => 2030, 'city' => 'Érd', 'street' => 'Balatoni út 51.'],
                ]]),
                new DynamicModel(['id' => 2, 'name' => 'Name', 'customerAddresses' => [
                    ['id' => 2, 'customer_id' => 2, 'zip_code' => 2030, 'city' => 'Érd', 'street' => 'Balatoni út 51.'],
                ]]),
                new DynamicModel(['id' => 3, 'name' => 'Name', 'customerAddresses' => [
                    ['id' => 3, 'customer_id' => 3, 'zip_code' => 2030, 'city' => 'Érd', 'street' => 'Balatoni út 51.'],
                ]]),
            ]],
            'model with relation model' => [Customer::class, ['id' => 'id', 'name' => 'name', 'customerAddresses' => 'customerAddresses'], [
                new DynamicModel(['id' => 1, 'name' => 'Name', 'customerAddresses' => [
                    new DynamicModel(['id' => 1, 'customer_id' => 1, 'zip_code' => 2030, 'city' => 'Érd', 'street' => 'Balatoni út 51.']),
                ]]),
                new DynamicModel(['id' => 2, 'name' => 'Name', 'customerAddresses' => [
                    new DynamicModel(['id' => 2, 'customer_id' => 2, 'zip_code' => 2030, 'city' => 'Érd', 'street' => 'Balatoni út 51.']),
                ]]),
                new DynamicModel(['id' => 3, 'name' => 'Name', 'customerAddresses' => [
                    new DynamicModel(['id' => 3, 'customer_id' => 3, 'zip_code' => 2030, 'city' => 'Érd', 'street' => 'Balatoni út 51.']),
                ]]),
            ]],
        ];
    }

    /**
     * @dataProvider dataProviderHydrateMultipleEntities
     *
     * @param $entityClass
     * @param $map
     * @param $models
     * @return array
     */
    public function testHydrateMultipleEntities($entityClass, $map, $models)
    {
        $hydrator = $this->mockHydrator($map);

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
     * @param $map
     * @param $data
     * @throws \yii\base\InvalidConfigException
     */
    public function testHydrateInto($entityClass, $map, $data)
    {
        $hydrator = $this->mockHydrator($map);

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
            'customer' => [Customer::class, ['id' => 'id', 'name' => 'name'], ['id' => 1, 'name' => 'Name']],
            'customerAddress' => [CustomerAddress::class, ['id' => 'id', 'customer_id' => 'customerId', 'zip_code' => 'zipCode', 'city' => 'city', 'street' => 'street'], ['id' => 1, 'customer_id' => 1, 'zip_code' => 2030, 'city' => 'Érd', 'street' => 'Balatoni út 51.']],
        ];
    }

    /**
     * @dataProvider dataProviderExtractHydratedModel
     * @param $entityClass
     * @param $map
     * @param $data
     * @throws \yii\base\InvalidConfigException
     */
    public function testExtractHydratedModel($entityClass, $map, $data)
    {
        $hydrator = $this->mockHydrator($map);

        $entity = $hydrator->hydrate($entityClass, $data);

        $this->assertEquals($data, $hydrator->extract($entity));
    }

    /**
     * @return ActiveHydrator
     */
    protected function mockHydrator($map)
    {
        return new ActiveHydrator($map);
    }

    /**
     * @param $entityClass
     * @param EntityInterface $entity
     * @param $data
     */
    protected function assertHydratedEntity($entityClass, EntityInterface $entity, $data): void
    {
        $this->assertInstanceOf($entityClass, $entity);

        $dataAttributes = $entity->getDataAttributes();
        $relationAttributes = array_keys($entity->relationMapping());

        $attributes = $data instanceof Model ? $data->attributes : $data;

        foreach ($attributes as $attribute => $expectedValue) {
            if (in_array($attribute, $relationAttributes) && !is_array($entity->$attribute)) {
                // on-to-one relation
                $this->assertInstanceOf($entity->relationMapping()[$attribute], $entity->$attribute);
            } elseif (in_array($attribute, $relationAttributes) && is_array($entity->$attribute)) {
                // on-to-many relation
                foreach ($entity->$attribute as $relationModel) {
                    $this->assertInstanceOf($entity->relationMapping()[$attribute], $relationModel);
                }
            } else {
                // data property
                $this->assertEquals($expectedValue, $entity->$attribute);
            }
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
}
