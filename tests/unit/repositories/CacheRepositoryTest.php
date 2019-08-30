<?php

namespace albertborsos\ddd\tests\repositories;

use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerCacheRepositoryInterface;
use albertborsos\ddd\tests\support\base\MockTrait;
use Codeception\PHPUnit\TestCase;

class CacheRepositoryTest extends TestCase
{
    use MockTrait;

    public function testPostfixedKey()
    {
        $customerIds = [1, 2, 3];

        $repository = \Yii::createObject(CustomerCacheRepositoryInterface::class);
        $repository->updateVipCustomers($customerIds);

        $this->assertEquals($customerIds, $repository->getVipCustomers());
    }

    public function testSetAndGetAndDelete()
    {
        $repository = \Yii::createObject(CustomerCacheRepositoryInterface::class);

        $key = 'test-key';
        $value = 'test-value';

        $this->assertTrue($repository->set($key, $value));
        $this->assertEquals($value, $repository->get($key));
        $this->assertTrue($repository->delete($key));
        $this->assertNotEquals($value, $repository->get($key));
        $this->assertFalse($repository->get($key));
    }

    public function entityDataProvider()
    {
        return [
            'customer' => [['id' => 1, 'name' => 'Albert']],
        ];
    }

    /**
     * @dataProvider entityDataProvider
     * @param $data
     * @throws \yii\base\InvalidConfigException
     */
    public function testFindByEntity($data)
    {
        $repository = \Yii::createObject(CustomerCacheRepositoryInterface::class);

        $customer = $repository->hydrate($data);

        $repository->storeEntity($customer);

        $this->assertEquals($customer, $repository->findByEntity($customer));

        $this->assertTrue($repository->delete($customer->getCacheKey()));
        $this->assertEmpty($repository->findByEntity($customer));
    }

    /**
     * @dataProvider entityDataProvider
     * @param $data
     * @throws \yii\base\InvalidConfigException
     */
    public function testFindById($data)
    {
        $repository = \Yii::createObject(CustomerCacheRepositoryInterface::class);

        $customer = $repository->hydrate($data);

        $repository->storeEntity($customer);

        $this->assertEquals($customer, $repository->findById($data['id']));

        $this->assertTrue($repository->delete($customer->getCacheKey()));
        $this->assertEmpty($repository->findById($data['id']));
    }

    /**
     * @dataProvider entityDataProvider
     * @param $data
     * @throws \yii\base\InvalidConfigException
     */
    public function testFindEntityByKey($data)
    {
        $repository = \Yii::createObject(CustomerCacheRepositoryInterface::class);

        $customer = $repository->hydrate($data);

        $repository->storeEntity($customer);

        $this->assertEquals($customer, $repository->findEntityByKey($customer->getCacheKey()));
        $this->assertTrue($repository->delete($customer->getCacheKey()));
        $this->assertEmpty($repository->findEntityByKey($customer->getCacheKey()));
    }
}
