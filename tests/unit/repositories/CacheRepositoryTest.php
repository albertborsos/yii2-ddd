<?php

namespace albertborsos\ddd\tests\repositories;

use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerCacheUpdaterInterface;
use albertborsos\ddd\tests\support\base\MockTrait;
use Codeception\PHPUnit\TestCase;

class CacheRepositoryTest extends TestCase
{
    use MockTrait;

    public function testPostfixedKey()
    {
        $customerIds = [1, 2, 3];

        $repository = \Yii::createObject(CustomerCacheUpdaterInterface::class);
        $repository->updateVipCustomers($customerIds);

        $this->assertEquals($customerIds, $repository->getVipCustomers());
    }

    public function testInsertAndFindAndUpdateAndDelete()
    {
        $repository = \Yii::createObject(CustomerCacheUpdaterInterface::class);
        $dataInsert = ['id' => 1, 'value' => microtime(false)];
        $dataUpdate = ['id' => 1, 'value' => microtime(false)];

        /** @var Customer $entity */
        // insert
        $entity = $repository->newEntity();
        $entity->setAttributes($dataInsert, false);
        $this->assertTrue($repository->insert($entity));
        $this->assertEquals($entity, $repository->findById($entity->id));
        // update
        $entity = $repository->newEntity();
        $entity->setAttributes($dataUpdate, false);
        $this->assertTrue($repository->update($entity));
        $this->assertEquals($entity, $repository->findById($entity->id));
        // delete
        $this->assertTrue($repository->delete($entity));
        $this->assertNotEquals($entity, $repository->findById($entity->id));
        $this->assertNull($repository->findById($entity->id));
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
        $repository = \Yii::createObject(CustomerCacheUpdaterInterface::class);
        $customer = $repository->hydrate($data);
        $repository->insert($customer);

        $this->assertEquals($customer, $repository->findById($data['id']));

        $this->assertTrue($repository->delete($customer));
        $this->assertEmpty($repository->findById($data['id']));
    }

    /**
     * @dataProvider entityDataProvider
     * @param $data
     * @throws \yii\base\InvalidConfigException
     */
    public function testFindById($data)
    {
        $repository = \Yii::createObject(CustomerCacheUpdaterInterface::class);

        $customer = $repository->hydrate($data);

        $repository->insert($customer);

        $this->assertEquals($customer, $repository->findById($data['id']));

        $this->assertTrue($repository->delete($customer));
        $this->assertEmpty($repository->findById($data['id']));
    }
}
