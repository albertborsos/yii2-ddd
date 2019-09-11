<?php

namespace albertborsos\ddd\tests\behaviors;

use albertborsos\ddd\repositories\AbstractActiveRepository;
use albertborsos\ddd\tests\fixtures\CustomerWithBehaviorsFixtures;
use albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerWithBehaviors;
use albertborsos\ddd\tests\support\base\infrastructure\db\customer\CustomerWithBehaviorsRepository;
use albertborsos\ddd\tests\support\base\infrastructure\db\customer\CustomerWithModifiedBehaviorsRepository;
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

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerWithBehaviorsRepository::class);

        $entity = $repository->hydrate($data);
        $this->assertTrue($repository->insert($entity));

        /** @var CustomerWithBehaviors $entity */
        $entity = $repository->findById($data['id']);

        $this->assertEquals(self::DEFAULT_USER_ID, $entity->createdBy);
        $this->assertEquals(self::DEFAULT_USER_ID, $entity->updatedBy);
        $this->assertEquals($entity->createdBy, $entity->updatedBy);
    }

    public function testInsertWhenUserisGuest()
    {
        $data = [
            'id' => 3,
            'name' => 'Test blameable attributes are filled on insert',
        ];

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerWithBehaviorsRepository::class);

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

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerWithBehaviorsRepository::class);
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

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerWithBehaviorsRepository::class);
        /** @var CustomerWithBehaviors $entity */
        $entity = $repository->findById($data['id']);

        $oldCreatedBy = $entity->createdBy;
        $oldUpdatedBy = $entity->updatedBy;

        $entity->setAttributes($data, false);
        $this->assertTrue($repository->update($entity));

        $entity = $repository->findById($data['id']);

        $this->assertEquals($oldCreatedBy, $entity->createdBy);
        $this->assertNull($entity->updatedBy);
    }

    public function testEmptyAttributes()
    {
        $this->authenticateUser(self::DEFAULT_USER_ID);

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerWithModifiedBehaviorsRepository::class);

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
