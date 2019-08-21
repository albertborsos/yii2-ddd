<?php

namespace albertborsos\ddd\tests\repositories;

use albertborsos\ddd\repositories\AbstractActiveRepository;
use albertborsos\ddd\tests\fixtures\CustomerFixtures;
use albertborsos\ddd\tests\support\base\Customer;
use albertborsos\ddd\tests\support\base\domains\customer\interfaces\CustomerActiveRepositoryInterface;
use albertborsos\ddd\tests\support\base\domains\customer\mysql\CustomerActiveRepository;
use albertborsos\ddd\tests\support\base\MockConfig;
use albertborsos\ddd\tests\support\base\MockTrait;
use albertborsos\ddd\tests\support\base\services\customer\CreateCustomerService;
use albertborsos\ddd\tests\support\base\services\customer\forms\CreateCustomerForm;
use Codeception\PHPUnit\TestCase;
use Codeception\Util\Debug;
use yii\base\Exception;
use yii\db\ActiveQueryInterface;
use yii\db\Transaction;
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
            'dataModelClass is null' => [CustomerActiveRepository::class, null],
            'dataModelClass is empty string' => [CustomerActiveRepository::class, ''],
            'dataModelClass is not implementing ActiveRecordInterface' => [CustomerActiveRepository::class, Customer::class],
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

    public function testFind()
    {
        $repository = \Yii::createObject(CustomerActiveRepositoryInterface::class);

        $this->assertInstanceOf(ActiveQueryInterface::class, $repository->find());
    }

    public function testFindOne()
    {
        $repository = \Yii::createObject(CustomerActiveRepositoryInterface::class);

        $fixtureId = $this->getFixture('customers')[0]['id'];

        $this->assertInstanceOf(\albertborsos\ddd\tests\support\base\domains\customer\entities\Customer::class, $repository->findOne($fixtureId));
    }

    public function testFindAll()
    {
        $repository = \Yii::createObject(CustomerActiveRepositoryInterface::class);

        $fixtureId = $this->getFixture('customers')[0]['id'];

        $entities = $repository->findAll(['id' => $fixtureId]);
        $this->assertCount(1, $entities);

        foreach ($entities as $entity) {
            $this->assertInstanceOf(\albertborsos\ddd\tests\support\base\domains\customer\entities\Customer::class, $entity);
        }
    }

    public function saveDataProvider()
    {
        return [
            'create customer' => [true, CustomerActiveRepositoryInterface::class, ['id' => 4, 'name' => 'Test to Save via repository']],
            'update customer' => [false, CustomerActiveRepositoryInterface::class, ['id' => 1, 'name' => 'Test to Save via repository']],
        ];
    }

    /**
     * @dataProvider saveDataProvider
     *
     * @param $isNewRecord
     * @param $repositoryClass
     * @param $data
     * @throws \yii\base\InvalidConfigException
     */
    public function testSave($isNewRecord, $repositoryClass, $data)
    {
        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject($repositoryClass);

        $entity = $repository->findOne($data['id']);
        if ($isNewRecord) {
            $this->assertNull($entity);
        } else {
            $this->assertInstanceOf($repository->getEntityClass(), $entity);
        }

        $entity = $repository->hydrate($data);
        $this->assertTrue($repository->save($entity));

        $entity = $repository->findOne($data['id']);
        $this->assertInstanceOf($repository->getEntityClass(), $entity);

        foreach ($entity->fields() as $attribute) {
            $this->assertEquals($data[$attribute], $entity->$attribute);
        }
    }

    public function testInsert()
    {
        $data = [
            'id' => 5,
            'name' => 'Test to Insert via repository',
        ];

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerActiveRepositoryInterface::class);

        $entity = $repository->hydrate($data);
        $this->assertTrue($repository->insert($entity));

        $entity = $repository->findOne($data['id']);
        $this->assertInstanceOf($repository->getEntityClass(), $entity);

        foreach ($entity->fields() as $attribute) {
            $this->assertEquals($data[$attribute], $entity->$attribute);
        }
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
        $repository = \Yii::createObject(CustomerActiveRepositoryInterface::class);

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
        $repository = \Yii::createObject(CustomerActiveRepositoryInterface::class);

        $entity = $repository->hydrate($data);
        $this->assertTrue($repository->update($entity));

        $entity = $repository->findOne($data['id']);
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
        $repository = \Yii::createObject(CustomerActiveRepositoryInterface::class);

        $entity = $repository->hydrate($data);
        $repository->update($entity);
    }

    public function deleteExistingRecordDataProvider()
    {
        return [
            'delete existing customer' => [CustomerActiveRepositoryInterface::class, 1],
        ];
    }

    /**
     * @dataProvider deleteExistingRecordDataProvider
     *
     * @param $isNewRecord
     * @param $repositoryClass
     * @param $data
     * @throws \yii\base\InvalidConfigException
     */
    public function testDeleteExistingRecord($repositoryClass, $recordId)
    {
        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject($repositoryClass);
        $entity = $repository->findOne($recordId);

        $this->assertInstanceOf($repository->getEntityClass(), $entity);
        $this->assertTrue($repository->delete($entity));

        $this->assertNull($repository->findOne($recordId));
    }

    /**
     * @expectedException TypeError
     *
     * @param $isNewRecord
     * @param $repositoryClass
     * @param $data
     * @throws \yii\base\InvalidConfigException
     */
    public function testDeleteNotExistingRecord()
    {
        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerActiveRepositoryInterface::class);
        $entity = $repository->findOne(4);

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
        $repository = \Yii::createObject(CustomerActiveRepositoryInterface::class);
        $entity = $repository->hydrate([]);

        $this->assertFalse($repository->delete($entity));
    }

    public function testBeginTransaction()
    {
        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerActiveRepositoryInterface::class);
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
        $repository = \Yii::createObject(CustomerActiveRepositoryInterface::class);
        $transaction = $repository->beginTransaction();

        try {
            $this->assertEmpty($repository->findOne($attributes));
            $form = new CreateCustomerForm($attributes);
            $this->assertTrue($form->validate());
            $service = new CreateCustomerService($form);
            $this->assertTrue($service->execute());
            $this->assertInstanceOf(\albertborsos\ddd\tests\support\base\domains\customer\entities\Customer::class, $repository->findOne($attributes));
            $transaction->commit();
        } catch (Exception $e) {
            return false;
        }

        $entity = $repository->findOne($attributes);
        $this->assertInstanceOf(\albertborsos\ddd\tests\support\base\domains\customer\entities\Customer::class, $entity);

        $repository->delete($entity);
    }

    public function testTransactionRollback()
    {
        $attributes = [
            'name' => 'Transaction Rollback',
        ];

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerActiveRepositoryInterface::class);
        $transaction = $repository->beginTransaction();

        try {
            $this->assertEmpty($repository->findOne($attributes));
            $form = new CreateCustomerForm($attributes);
            $this->assertTrue($form->validate());
            $service = new CreateCustomerService($form);
            $this->assertTrue($service->execute());
            $this->assertInstanceOf(\albertborsos\ddd\tests\support\base\domains\customer\entities\Customer::class, $repository->findOne($attributes));

            throw new Exception('rollback');
        } catch (Exception $e) {
            $transaction->rollBack();
            $this->assertEmpty($repository->findOne($attributes));
        }
    }
}
