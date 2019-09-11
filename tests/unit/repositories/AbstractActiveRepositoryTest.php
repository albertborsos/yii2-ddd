<?php

namespace albertborsos\ddd\tests\repositories;

use albertborsos\ddd\repositories\AbstractActiveRepository;
use albertborsos\ddd\tests\fixtures\CustomerFixtures;
use albertborsos\ddd\tests\support\base\Customer;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerRepositoryInterface;
use albertborsos\ddd\tests\support\base\infrastructure\db\customer\CustomerRepository;
use albertborsos\ddd\tests\support\base\MockConfig;
use albertborsos\ddd\tests\support\base\MockTrait;
use albertborsos\ddd\tests\support\base\services\customer\CreateCustomerService;
use albertborsos\ddd\tests\support\base\services\customer\forms\CreateCustomerForm;
use Codeception\PHPUnit\TestCase;
use TypeError;
use yii\base\Exception;
use yii\test\FixtureTrait;

class AbstractActiveRepositoryTest extends TestCase
{
    use MockTrait;
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

    public function invalidDataModelClassDataProvider()
    {
        return [
            'dataModelClass is null' => [CustomerRepository::class, null],
            'dataModelClass is empty string' => [CustomerRepository::class, ''],
            'dataModelClass is not implementing ActiveRecordInterface' => [CustomerRepository::class, Customer::class],
        ];
    }

    /**
     * @dataProvider invalidDataModelClassDataProvider
     * @expectedException \yii\base\InvalidConfigException
     * @expectedExceptionMessageRegExp  /\$dataModelClass must implements `yii\\db\\ActiveRecordInterface`$/
     */
    public function testMissingDataModelClass($repositoryClass, $dataModelClass)
    {
        $this->mockObject(MockConfig::create($repositoryClass, ['dataModelClass' => $dataModelClass]));
    }

    public function testInsert()
    {
        $data = [
            'id' => 5,
            'name' => 'Test to Insert via repository',
        ];

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerRepositoryInterface::class);

        $entity = $repository->hydrate($data);
        $this->assertTrue($repository->insert($entity));

        $entity = $repository->findById($data['id']);
        $this->assertInstanceOf($repository->getEntityClass(), $entity);

        foreach ($entity->fields() as $attribute) {
            $this->assertEquals($data[$attribute], $entity->$attribute);
        }
    }

    /**
     * @expectedException \yii\base\InvalidArgumentException
     */
    public function testCallUpdateForNonExistingRecord()
    {
        $data = [
            'id' => 6,
            'name' => 'Test to Update via repository',
        ];

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerRepositoryInterface::class);

        $entity = $repository->hydrate($data);
        $repository->update($entity);
    }

    /**
     * @expectedException \yii\base\InvalidArgumentException
     */
    public function testCallInsertForExistingRecord()
    {
        $data = [
            'id' => 1,
            'name' => 'Test to Insert via repository',
        ];

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerRepositoryInterface::class);

        $entity = $repository->hydrate($data);
        $repository->insert($entity);
    }

    public function testUpdate()
    {
        $data = [
            'id' => 1,
            'name' => 'Test to Update via repository',
        ];

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerRepositoryInterface::class);

        /** @var \albertborsos\ddd\tests\support\base\domains\customer\entities\Customer $entity */
        $entity = $repository->findById($data['id']);
        $entity->setAttributes($data, false);

        $this->assertTrue($repository->update($entity));

        $entity = $repository->findById($data['id']);
        $this->assertInstanceOf($repository->getEntityClass(), $entity);

        foreach ($entity->fields() as $attribute) {
            $this->assertEquals($data[$attribute], $entity->$attribute);
        }
    }

    public function testUpdateWithNoModification()
    {
        $data = [
            'id' => 1,
            'name' => 'Albert',
        ];

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerRepositoryInterface::class);

        $entity = $repository->hydrate($data);
        $this->assertTrue($repository->update($entity));

        $entity = $repository->findById($data['id']);
        $this->assertInstanceOf($repository->getEntityClass(), $entity);

        foreach ($entity->fields() as $attribute) {
            $this->assertEquals($data[$attribute], $entity->$attribute);
        }
    }

    public function deleteExistingRecordDataProvider()
    {
        return [
            'delete existing customer' => [CustomerRepositoryInterface::class, 1],
        ];
    }

    /**
     * @dataProvider deleteExistingRecordDataProvider
     *
     * @param $repositoryClass
     * @param $recordId
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function testDeleteExistingRecord($repositoryClass, $recordId)
    {
        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject($repositoryClass);
        $entity = $repository->findById($recordId);

        $this->assertInstanceOf($repository->getEntityClass(), $entity);
        $this->assertTrue($repository->delete($entity));

        $this->assertNull($repository->findById($recordId));
    }

    /**
     * @expectedException TypeError
     *
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function testDeleteNotExistingRecord()
    {
        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerRepositoryInterface::class);
        $entity = $repository->findById(4);

        $this->assertNull($entity);
        $repository->delete($entity);
    }

    /**
     * @param $isNewRecord
     * @param $repositoryClass
     * @param $data
     * @throws \yii\base\InvalidConfigException
     */
    public function testDeleteEmptyEntity()
    {
        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerRepositoryInterface::class);
        $entity = $repository->hydrate([]);

        $this->assertFalse($repository->delete($entity));
    }

    public function testBeginTransaction()
    {
        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerRepositoryInterface::class);
        $transaction = $repository->beginTransaction();
        $this->assertNotNull($transaction);
        $this->assertTrue($transaction->isActive);

        $transaction->rollBack();
    }

    public function testTransactionCommit()
    {
        $attributes = [
            'name' => 'Transaction Commit',
        ];

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerRepositoryInterface::class);
        $transaction = $repository->beginTransaction();

        try {
            $this->assertEmpty($repository->findById($attributes));
            $form = new CreateCustomerForm($attributes);
            $this->assertTrue($form->validate());
            $service = new CreateCustomerService($form);
            $this->assertTrue($service->execute());
            $this->assertInstanceOf(\albertborsos\ddd\tests\support\base\domains\customer\entities\Customer::class, $repository->findById($service->getId()));
            $transaction->commit();
        } catch (Exception $e) {
            return false;
        }

        $entity = $repository->findById($service->getId());
        $this->assertInstanceOf(\albertborsos\ddd\tests\support\base\domains\customer\entities\Customer::class, $entity);

        $repository->delete($entity);
    }

    public function testTransactionRollback()
    {
        $attributes = [
            'name' => 'Transaction Rollback',
        ];

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerRepositoryInterface::class);
        $transaction = $repository->beginTransaction();

        try {
            $this->assertEmpty($repository->findById($attributes));
            $form = new CreateCustomerForm($attributes);
            $this->assertTrue($form->validate());
            $service = new CreateCustomerService($form);
            $this->assertTrue($service->execute());
            $this->assertInstanceOf(\albertborsos\ddd\tests\support\base\domains\customer\entities\Customer::class, $repository->findById($service->getId()));

            throw new Exception('rollback');
        } catch (Exception $e) {
            $transaction->rollBack();
            $this->assertEmpty($repository->findById($service->getId()));
        }
    }
}
