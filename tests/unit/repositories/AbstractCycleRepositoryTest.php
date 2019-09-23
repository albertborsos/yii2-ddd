<?php

namespace albertborsos\ddd\tests\repositories;

use albertborsos\ddd\repositories\AbstractCycleRepository;
use albertborsos\ddd\tests\fixtures\CustomerFixtures;
use albertborsos\ddd\tests\support\base\infrastructure\cycle\customer\CustomerRepository;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerRepositoryInterface;
use albertborsos\ddd\tests\support\base\MockTrait;
use albertborsos\ddd\tests\support\base\services\customer\CreateCustomerService;
use albertborsos\ddd\tests\support\base\services\customer\forms\CreateCustomerForm;
use Codeception\PHPUnit\TestCase;
use Cycle\ORM\Transaction;
use TypeError;
use yii\base\Exception;
use yii\test\FixtureTrait;

class AbstractCycleRepositoryTest extends TestCase
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
        \Yii::$app->setContainer([
            'definitions' => [
                CustomerRepositoryInterface::class => CustomerRepository::class,
            ],
        ]);
        $this->initFixtures();
        \Yii::$app->cycle->cleanHeap();
    }

    public function testInsert()
    {
        $data = [
            'id' => 5,
            'name' => 'Test to Insert via repository',
        ];

        /** @var AbstractCycleRepository $repository */
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

        /** @var AbstractCycleRepository $repository */
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

        /** @var AbstractCycleRepository $repository */
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

        /** @var AbstractCycleRepository $repository */
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

        /** @var AbstractCycleRepository $repository */
        $repository = \Yii::createObject(CustomerRepositoryInterface::class);

        $entity = $repository->findById($data['id']);
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
        /** @var AbstractCycleRepository $repository */
        $repository = \Yii::createObject(CustomerRepositoryInterface::class);
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
        /** @var AbstractCycleRepository $repository */
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
     * @throws \Throwable
     */
    public function testDeleteEmptyEntity()
    {
        /** @var AbstractCycleRepository $repository */
        $repository = \Yii::createObject(CustomerRepositoryInterface::class);
        $entity = $repository->hydrate([]);

        $this->assertFalse($repository->delete($entity));
    }

    public function testBeginTransaction()
    {
        /** @var AbstractCycleRepository $repository */
        $repository = \Yii::createObject(CustomerRepositoryInterface::class);
        $transaction = $repository->beginTransaction();
        $this->assertNotNull($transaction);
        $this->assertInstanceOf(Transaction::class, $transaction);
    }

    public function testTransactionRun()
    {
        $attributes = [
            'name' => 'Transaction Run',
        ];

        /** @var AbstractCycleRepository $repository */
        $repository = \Yii::createObject(CustomerRepositoryInterface::class);
        $transaction = $repository->beginTransaction();

        try {
            $form = new CreateCustomerForm($attributes);
            $this->assertTrue($form->validate());
            $service = new CreateCustomerService($form);
            $this->assertTrue($service->execute());
            $this->assertNotNull($service->getId());
            $transaction->run();
        } catch (Exception $e) {
            return false;
        }

        $entity = $repository->findById($service->getId());
        $this->assertInstanceOf(\albertborsos\ddd\tests\support\base\domains\customer\entities\Customer::class, $entity);

        $repository->delete($entity);
    }
}
