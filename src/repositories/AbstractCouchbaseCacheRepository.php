<?php

namespace albertborsos\ddd\repositories;

use albertborsos\couchbase\Connection;
use albertborsos\couchbase\Exception;
use albertborsos\ddd\interfaces\EntityInterface;
use yii\di\Instance;

abstract class AbstractCouchbaseCacheRepository extends AbstractRepository
{
    /**
     * @var string|Connection
     */
    protected $couchbase = 'couchbase';

    /**
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->couchbase = Instance::ensure($this->couchbase, Connection::class);
    }

    /**
     * @param $id
     * @return EntityInterface|null
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function findById($id): ?EntityInterface
    {
        /** @var EntityInterface $model */
        $model = $this->hydrate(['id' => $id]);

        return $this->findEntityByKey($model->getCacheKey());
    }

    /**
     * @param EntityInterface $entity
     * @param array $attributes
     * @param bool $addNotConditionForPrimaryKeys
     * @return bool
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function exists(EntityInterface $entity, $attributes = [], $addNotConditionForPrimaryKeys = false): bool
    {
        return !empty($this->findEntityByKey($entity->getCacheKey($attributes)));
    }

    /**
     * @param EntityInterface $entity
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function insert(EntityInterface $entity, $runValidation = true, $attributeNames = null): bool
    {
        $this->couchbase->getBucket()->set($entity->getCacheKey(), $entity->getDataAttributes(), []);
        return true;
    }

    /**
     * @param EntityInterface $entity
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function update(EntityInterface $entity, $runValidation = true, $attributeNames = null): bool
    {
        $this->couchbase->getBucket()->set($entity->getCacheKey(), $entity->getDataAttributes(), []);
        return true;
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function delete(EntityInterface $entity): bool
    {
        $this->couchbase->getBucket()->delete($entity->getCacheKey());
        return true;
    }

    /**
     * @param EntityInterface $entity
     * @return EntityInterface|null
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function findByEntity(EntityInterface $entity): ?EntityInterface
    {
        return $this->findEntityByKey($entity->getCacheKey());
    }

    /**
     * @param string $key
     * @return EntityInterface|null
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function findEntityByKey(string $key): ?EntityInterface
    {
        $couchbaseModel = $this->couchbase->getBucket()->get($key);

        if ($couchbaseModel === null) {
            return null;
        }

        return $this->hydrate((array)$couchbaseModel->value);
    }
}
