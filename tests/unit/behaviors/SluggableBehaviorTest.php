<?php

namespace albertborsos\ddd\tests\behaviors;

use albertborsos\ddd\interfaces\RepositoryInterface;
use albertborsos\ddd\tests\fixtures\CustomerWithBehaviorsFixtures;
use albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerWithBehaviors;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerWithBehaviorsRepositoryInterface;
use Codeception\PHPUnit\TestCase;
use yii\test\FixtureTrait;

class SluggableBehaviorTest extends TestCase
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
        \Yii::$app->cycle->cleanHeap();
    }

    public function testInsert()
    {
        $data = [
            'id' => 3,
            'name' => 'Test slug attribute is filled on insert',
        ];

        /** @var RepositoryInterface $repository */
        $repository = \Yii::createObject(CustomerWithBehaviorsRepositoryInterface::class);

        $entity = $repository->hydrate($data);
        $this->assertTrue($repository->insert($entity));

        /** @var CustomerWithBehaviors $entity */
        $entity = $repository->findById($data['id']);

        $this->assertNotEmpty($entity->slug);
    }

    public function testUpdate()
    {
        $data = [
            'id' => 1,
            'name' => 'Test slug attribute is modified on update',
        ];

        /** @var RepositoryInterface $repository */
        $repository = \Yii::createObject(CustomerWithBehaviorsRepositoryInterface::class);
        /** @var CustomerWithBehaviors $entity */
        $entity = $repository->findById($data['id']);

        $oldSlug = $entity->slug;

        $entity->setAttributes($data, false);
        $this->assertTrue($repository->update($entity));

        $entity = $repository->findById($data['id']);

        $this->assertNotEquals($oldSlug, $entity->slug);
    }

    public function testUpdateWithoutModification()
    {
        $data = [
            'id' => 1,
            'name' => 'Albert',
        ];

        /** @var RepositoryInterface $repository */
        $repository = \Yii::createObject(CustomerWithBehaviorsRepositoryInterface::class);
        /** @var CustomerWithBehaviors $entity */
        $entity = $repository->findById($data['id']);

        $oldSlug = $entity->slug;

        $entity->setAttributes($data, false);
        $this->assertTrue($repository->update($entity));

        $entity = $repository->findById($data['id']);

        $this->assertEquals($oldSlug, $entity->slug);
    }

    public function testUniqueSlug()
    {
        $data = [
            'id' => 3,
            'name' => 'Albert',
        ];

        /** @var RepositoryInterface $repository */
        $repository = \Yii::createObject(CustomerWithBehaviorsRepositoryInterface::class);
        /** @var CustomerWithBehaviors $entity */
        $entity = $repository->hydrate($data);
        $this->assertTrue($repository->insert($entity));

        $entity = $repository->findById($data['id']);

        $this->assertNotEquals('albert', $entity->slug);
    }
}
