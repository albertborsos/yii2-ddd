<?php

namespace albertborsos\ddd\tests\unit\models;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use albertborsos\ddd\tests\support\base\MockTrait;
use Codeception\PHPUnit\TestCase;
use yii\base\DynamicModel;

class AbstractEntityTest extends TestCase
{
    use MockTrait;

    public function testGetPrimaryKey()
    {
        $entity = \Yii::createObject(Customer::class);

        $this->assertEquals($entity->getPrimaryKey(), ['id']);
    }

    public function dataProviderSetPrimaryKey()
    {
        return array_merge(
            $this->dataProviderInvalidPrimaryKeys(),
            $this->dataProviderValidPrimaryKeys()
        );
    }

    public function dataProviderInvalidPrimaryKeys()
    {
        return [
            'no primary key (null)' => [['id' => 1, 'name' => 'Name'], Customer::class, ['getPrimaryKey' => null]],
            'no primary key (empty string)' => [['id' => 1, 'name' => 'Name'], Customer::class, ['getPrimaryKey' => '']],
            'no primary key (array with empty string)' => [['id' => 1, 'name' => 'Name'], Customer::class, ['getPrimaryKey' => ['']]],
            'no primary key (array with null value)' => [['id' => 1, 'name' => 'Name'], Customer::class, ['getPrimaryKey' => [null]]],
        ];
    }

    /**
     * @return array
     */
    public function dataProviderValidPrimaryKeys(): array
    {
        return [
            'standard primary key (string)' => [['id' => 1, 'name' => 'Name'], Customer::class, ['getPrimaryKey' => 'id']],
            'standard primary key (array)' => [['id' => 1, 'name' => 'Name'], Customer::class, ['getPrimaryKey' => ['id']]],
            'composite key' => [['id' => 1, 'name' => 'Name'], Customer::class, ['getPrimaryKey' => ['id', 'name']]],
        ];
    }

    /**
     * @dataProvider dataProviderSetPrimaryKey
     */
    public function testSetPrimaryKey($modelAttributes, $entityClass, $entitySettings)
    {
        $model = new DynamicModel($modelAttributes);

        /** @var EntityInterface $entity */
        $entity = $this->mockObject(['class' => $entityClass, 'settings' => $entitySettings]);
        $entity->setPrimaryKey($model);

        foreach ($entity->attributes as $attribute => $value) {
            $isPrimaryKey = in_array($attribute, is_array($entity->getPrimaryKey()) ? $entity->getPrimaryKey() : [$entity->getPrimaryKey()]);
            $this->assertEquals($isPrimaryKey ? $value : null, $entity->$attribute);
        }
    }

    /**
     * @dataProvider dataProviderInvalidPrimaryKeys
     * @expectedException \yii\base\InvalidConfigException
     */
    public function testCacheKeyWithInvalidPrimaryKeysWillThrowsException($modelAttributes, $entityClass, $entitySettings)
    {
        $model = new DynamicModel($modelAttributes);
        /** @var EntityInterface $entity */
        $entity = $this->mockObject(['class' => $entityClass, 'settings' => $entitySettings]);
        $entity->setPrimaryKey($model);

        $entity->getCacheKey();
    }

    /**
     * @dataProvider dataProviderValidPrimaryKeys
     */
    public function testCacheKey($modelAttributes, $entityClass, $entitySettings)
    {
        $model = new DynamicModel($modelAttributes);
        /** @var EntityInterface $entity */
        $entity = $this->mockObject(['class' => $entityClass, 'settings' => $entitySettings, 'attributes' => $modelAttributes]);
        $entity->setPrimaryKey($model);

        $this->assertNotEmpty($entity->getCacheKey());
        $this->assertNotEquals($entity->getCacheKey(), get_class($entity));
    }

    /**
     * @dataProvider dataProviderValidPrimaryKeys
     */
    public function testCacheKeyWithCustomKeyAttributes($modelAttributes, $entityClass, $entitySettings)
    {
        $model = new DynamicModel($modelAttributes);
        /** @var EntityInterface $entity */
        $entity = $this->mockObject(['class' => $entityClass, 'settings' => $entitySettings, 'attributes' => $modelAttributes]);
        $entity->setPrimaryKey($model);

        $this->assertNotEmpty($entity->getCacheKey(['name']));
        $this->assertNotEquals($entity->getCacheKey(['name']), get_class($entity));
        $this->assertNotEquals($entity->getCacheKey(['name']), $entity->getCacheKey());
    }

    /**
     * @dataProvider dataProviderValidPrimaryKeys
     */
    public function testCacheKeyWithPostfix($modelAttributes, $entityClass, $entitySettings)
    {
        $model = new DynamicModel($modelAttributes);
        /** @var EntityInterface $entity */
        $entity = $this->mockObject(['class' => $entityClass, 'settings' => $entitySettings, 'attributes' => $modelAttributes]);
        $entity->setPrimaryKey($model);

        $postfix = 'postfix';

        $this->assertNotEmpty($entity->getCacheKey([], $postfix));
        $this->assertNotEquals($entity->getCacheKey([], $postfix), get_class($entity));
        $this->assertNotEquals($entity->getCacheKey([], $postfix), $entity->getCacheKey());

        $this->assertEquals($entity->getCacheKey([], $postfix), implode('_', [$entity->getCacheKey(), $postfix]));
    }

    /**
     * @dataProvider dataProviderValidPrimaryKeys
     */
    public function testIsNew($modelAttributes, $entityClass, $entitySettings)
    {
        /** @var EntityInterface $entity */
        $entity = $this->mockObject(['class' => $entityClass, 'settings' => $entitySettings, 'attributes' => $modelAttributes]);
        $this->assertFalse($entity->isNew());

        foreach ((array)$entity->getPrimaryKey() as $key) {
            $entity->{$key} = null;
        }

        $this->assertTrue($entity->isNew());
    }
}
