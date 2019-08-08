<?php

namespace albertborsos\ddd\tests\unit\hydrators;

use albertborsos\ddd\hydrators\ActiveHydrator;
use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use albertborsos\ddd\tests\support\base\MockTrait;
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
}
