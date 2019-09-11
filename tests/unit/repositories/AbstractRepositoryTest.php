<?php

namespace albertborsos\ddd\tests\repositories;

use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerRepositoryInterface;
use albertborsos\ddd\tests\support\base\infrastructure\db\customer\CustomerRepository;
use albertborsos\ddd\tests\support\base\infrastructure\db\customer\InvalidCustomerActiveRepository;
use albertborsos\ddd\tests\support\base\infrastructure\db\customer\InvalidEntityCustomerRepository;
use albertborsos\ddd\tests\support\base\infrastructure\db\customer\InvalidHydratorCustomerRepository;
use albertborsos\ddd\tests\support\base\MockConfig;
use albertborsos\ddd\tests\support\base\MockTrait;
use Codeception\PHPUnit\TestCase;

class AbstractRepositoryTest extends TestCase
{
    use MockTrait;

    public function testHydrateInto()
    {
        $repository = \Yii::createObject(CustomerRepositoryInterface::class);

        $entity = $repository->hydrate([]);

        $data = ['id' => 9, 'name' => 'hydrate into'];
        $hydratedEntity = $repository->hydrateInto($entity, $data);

        foreach ($data as $attribute => $value) {
            $this->assertEquals($value, $hydratedEntity->$attribute);
        }
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     * @expectedExceptionMessageRegExp /\$entityClass must implements `albertborsos\\ddd\\interfaces\\EntityInterface`$/
     */
    public function testInvalidEntityClass()
    {
        \Yii::createObject(InvalidEntityCustomerRepository::class);
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     * @expectedExceptionMessageRegExp /\$hydrator must implements `albertborsos\\ddd\\interfaces\\HydratorInterface`$/
     */
    public function testInvalidHydratorClass()
    {
        new InvalidHydratorCustomerRepository();
    }
}
