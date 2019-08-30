<?php

namespace albertborsos\ddd\tests\behaviors;

use albertborsos\ddd\repositories\AbstractActiveRepository;
use albertborsos\ddd\tests\fixtures\CustomerWithBehaviorsFixtures;
use albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerWithBehaviors;
use albertborsos\ddd\tests\support\base\domains\customer\mysql\CustomerWithBehaviorsActiveRepository;
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
    }

    public function testInsert()
    {
        $data = [
            'id' => 3,
            'name' => 'Test slug attribute is filled on insert',
        ];

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerWithBehaviorsActiveRepository::class);

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

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerWithBehaviorsActiveRepository::class);
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

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerWithBehaviorsActiveRepository::class);
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

        /** @var AbstractActiveRepository $repository */
        $repository = \Yii::createObject(CustomerWithBehaviorsActiveRepository::class);
        /** @var CustomerWithBehaviors $entity */
        $entity = $repository->hydrate($data);
        $this->assertTrue($repository->insert($entity));

        $entity = $repository->findById($data['id']);

        $this->assertNotEquals('albert', $entity->slug);
    }
}
