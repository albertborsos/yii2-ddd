<?php

namespace albertborsos\ddd\tests\behaviors;

use albertborsos\ddd\interfaces\RepositoryInterface;
use albertborsos\ddd\tests\fixtures\CustomerWithBehaviorsFixtures;
use albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerWithBehaviors;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerWithBehaviorsRepositoryInterface;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerWithModifiedBehaviorsRepositoryInterface;
use Codeception\PHPUnit\TestCase;
use yii\test\FixtureTrait;

class BlameableBehaviorTest extends TestCase
{
    use FixtureTrait;

    const DEFAULT_USER_ID = 10;
    const UPDATER_USER_ID = 11;

    public function fixtures()
    {
        return [
            'customers' => CustomerWithBehaviorsFixtures::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->initFixtures();
        \Yii::$app->cycle->cleanHeap();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->logoutUser();
    }

    public function testInsert()
    {
        $this->authenticateUser(self::DEFAULT_USER_ID);

        $data = [
            'id' => 3,
            'name' => 'Test blameable attributes are filled on insert',
        ];

        /** @var RepositoryInterface $repository */
        $repository = \Yii::createObject(CustomerWithBehaviorsRepositoryInterface::class);

        $entity = $repository->hydrate($data);
        $this->assertTrue($repository->insert($entity));

        /** @var CustomerWithBehaviors $entity */
        $entity = $repository->findById($data['id']);

        $this->assertEquals(self::DEFAULT_USER_ID, $entity->createdBy);
        $this->assertEquals(self::DEFAULT_USER_ID, $entity->updatedBy);
        $this->assertEquals($entity->createdBy, $entity->updatedBy);
    }

    public function testInsertWhenUserIsGuest()
    {
        $data = [
            'id' => 3,
            'name' => 'Test blameable attributes are filled on insert',
        ];

        /** @var RepositoryInterface $repository */
        $repository = \Yii::createObject(CustomerWithBehaviorsRepositoryInterface::class);

        $entity = $repository->hydrate($data);
        $this->assertTrue($repository->insert($entity));

        /** @var CustomerWithBehaviors $entity */
        $entity = $repository->findById($data['id']);

        $this->assertNull($entity->createdBy);
        $this->assertNull($entity->updatedBy);
    }

    public function testUpdate()
    {
        $this->authenticateUser(self::UPDATER_USER_ID);

        $data = [
            'id' => 1,
            'name' => 'Test updatedBy timestamp attribute is modified on update',
        ];

        /** @var RepositoryInterface $repository */
        $repository = \Yii::createObject(CustomerWithBehaviorsRepositoryInterface::class);
        /** @var CustomerWithBehaviors $entity */
        $entity = $repository->findById($data['id']);

        $oldCreatedBy = $entity->createdBy;
        $oldUpdatedBy = $entity->updatedBy;

        $entity->setAttributes($data, false);
        $this->assertTrue($repository->update($entity));

        $entity = $repository->findById($data['id']);

        $this->assertEquals($oldCreatedBy, $entity->createdBy);
        $this->assertEquals(self::UPDATER_USER_ID, $entity->updatedBy);
        $this->assertNotEquals($oldUpdatedBy, $entity->updatedBy);
    }

    public function testUpdateWhenUserIsGuest()
    {
        $data = [
            'id' => 1,
            'name' => 'Test updatedBy timestamp attribute is modified on update',
        ];

        /** @var RepositoryInterface $repository */
        $repository = \Yii::createObject(CustomerWithBehaviorsRepositoryInterface::class);
        /** @var CustomerWithBehaviors $entity */
        $entity = $repository->findById($data['id']);

        $oldCreatedBy = $entity->createdBy;
        $oldUpdatedBy = $entity->updatedBy;

        $entity->setAttributes($data, false);
        $this->assertTrue($repository->update($entity));

        $entity = $repository->findById($data['id']);

        $this->assertEquals($oldCreatedBy, $entity->createdBy);
        $this->assertNotNull($oldUpdatedBy);
        $this->assertNull($entity->updatedBy);
    }

    public function testEmptyAttributes()
    {
        $this->authenticateUser(self::DEFAULT_USER_ID);

        /** @var RepositoryInterface $repository */
        $repository = \Yii::createObject(CustomerWithModifiedBehaviorsRepositoryInterface::class);

        $data = [
            'id' => 3,
            'name' => 'test only createdBy',
        ];

        /** @var CustomerWithBehaviors $entity */
        $entity = $repository->hydrate($data);

        $this->assertTrue($repository->insert($entity));

        /** @var CustomerWithBehaviors $entity */
        $entity = $repository->findById($data['id']);

        $this->assertNotEmpty($entity->createdBy);
        $this->assertEmpty($entity->updatedBy);
    }

    private function authenticateUser(int $id)
    {
        \Yii::$app->get('user')->login($id);
    }

    private function logoutUser()
    {
        \Yii::$app->get('user')->logout();
    }
}
