<?php

namespace albertborsos\ddd\tests\repositories;

use albertborsos\ddd\tests\support\base\domains\customer\interfaces\CustomerActiveRepositoryInterface;
use albertborsos\ddd\tests\support\base\domains\customer\mysql\CustomerActiveRepository;
use albertborsos\ddd\tests\support\base\domains\customer\mysql\InvalidCustomerActiveRepository;
use albertborsos\ddd\tests\support\base\domains\customer\mysql\InvalidEntityCustomerActiveRepository;
use albertborsos\ddd\tests\support\base\domains\customer\mysql\InvalidHydratorCustomerActiveRepository;
use albertborsos\ddd\tests\support\base\MockConfig;
use albertborsos\ddd\tests\support\base\MockTrait;
use Codeception\PHPUnit\TestCase;
use yii\base\Model;

class AbstractRepositoryTest extends TestCase
{
    use MockTrait;

    public function testHydrateInto()
    {
        $repository = \Yii::createObject(CustomerActiveRepositoryInterface::class);

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
        \Yii::createObject(InvalidEntityCustomerActiveRepository::class);
    }

    /**
     * @expectedException \yii\base\InvalidConfigException
     * @expectedExceptionMessageRegExp /\$hydrator must implements `albertborsos\\ddd\\interfaces\\HydratorInterface`$/
     */
    public function testInvalidHydratorClass()
    {
        new InvalidHydratorCustomerActiveRepository();
    }
}