<?php

namespace albertborsos\ddd\tests\behaviors;

use albertborsos\ddd\repositories\AbstractActiveRepository;
use albertborsos\ddd\tests\fixtures\CustomerWithBehaviorsFixtures;
use albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerWithBehaviors;
use albertborsos\ddd\tests\support\base\domains\customer\mysql\CustomerWithBehaviorsActiveRepository;
use albertborsos\ddd\tests\support\base\domains\customer\mysql\CustomerWithModifiedBehaviorsActiveRepository;
use Codeception\PHPUnit\TestCase;
use yii\test\FixtureTrait;

class TimestampBehaviorTest extends TestCase
{
    use FixtureTrait;

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

    public function testInsert()
    {
        $data = [
            'id' => 3,
            'name' => 'Test timestamp attributes are filled on insert',
        ];

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerWithBehaviorsActiveRepository::class);

        $entity = $repository->hydrate($data);
        $this->assertTrue($repository->insert($entity));

        /** @var CustomerWithBehaviors $entity */
        $entity = $repository->findById($data['id']);

        $this->assertNotEmpty($entity->createdAt);
        $this->assertNotEmpty($entity->updatedAt);
        $this->assertEquals($entity->createdAt, $entity->updatedAt);
    }

    public function testUpdate()
    {
        $data = [
            'id' => 1,
            'name' => 'Test updatedAt timestamp attribute is modified on update',
        ];

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerWithBehaviorsActiveRepository::class);
        /** @var CustomerWithBehaviors $entity */
        $entity = $repository->findById($data['id']);

        $oldCreatedAt = $entity->createdAt;
        $oldUpdatedAt = $entity->updatedAt;

        $entity->setAttributes($data, false);
        $this->assertTrue($repository->update($entity));

        $entity = $repository->findById($data['id']);

        $this->assertNotEmpty($entity->updatedAt);
        $this->assertNotEquals($oldUpdatedAt, $entity->updatedAt);
        $this->assertNotEmpty($entity->createdAt);
        $this->assertEquals($oldCreatedAt, $entity->createdAt);
    }

    public function testEmptyAttributes()
    {
        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerWithModifiedBehaviorsActiveRepository::class);

        $data = [
            'id' => 3,
            'name' => 'test only createdAt',
        ];

        /** @var CustomerWithBehaviors $entity */
        $entity = $repository->hydrate($data);

        $this->assertTrue($repository->insert($entity));

        /** @var CustomerWithBehaviors $entity */
        $entity = $repository->findById($data['id']);

        $this->assertNotEmpty($entity->createdAt);
        $this->assertEmpty($entity->updatedAt);
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     * @expectedExceptionMessageRegExp /must be used with `albertborsos\\ddd\\base\\EntityEvent`$/
     */
    public function testInvalidEventException()
    {
        /** @var CustomerWithModifiedBehaviorsActiveRepository $repository */
        $repository = \Yii::createObject(CustomerWithModifiedBehaviorsActiveRepository::class);
        $repository->fakeEventClass = true;

        $data = [
            'id' => 3,
            'name' => 'test invalid event exception',
        ];

        /** @var CustomerWithBehaviors $entity */
        $entity = $repository->hydrate($data);

        $this->assertTrue($repository->insert($entity));
    }
}
