<?php

namespace albertborsos\ddd\repositories;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\traits\PostfixedKeyTrait;
use yii\base\NotSupportedException;
use yii\caching\CacheInterface;
use yii\di\Instance;

/**
 * Class CacheRepository
 * Use this repository with a cache component which implements `\yii\caching\CacheInterface`
 *
 * @package albertborsos\ddd\repositories
 * @since 2.0.0
 */
abstract class AbstractCacheRepository extends AbstractRepository
{
    use PostfixedKeyTrait;

    /** @var string|CacheInterface $cache  */
    protected $cache = 'cache';

    /**
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->cache = Instance::ensure($this->cache, CacheInterface::class);
    }

    /**
     * @param $id
     * @return EntityInterface|null
     */
    public function findById($id): ?EntityInterface
    {
        /** @var EntityInterface $entity */
        $entity = $this->hydrate(['id' => $id]);

        return $this->findEntityByKey($entity->getCacheKey());
    }

    /**
     * @param EntityInterface $entity
     * @param array $attributes
     * @param array $filter
     * @return bool
     */
    public function exists(EntityInterface $entity, $attributes = [], $filter = []): bool
    {
        return !empty($this->findEntityByKey($entity->getCacheKey($attributes)));
    }

    /**
     * @param EntityInterface $entity
     * @param bool $runValidation
     * @param null $attributeNames
     * @param bool $checkIsNewRecord
     * @return bool
     */
    public function insert(EntityInterface $entity, $runValidation = true, $attributeNames = null, $checkIsNewRecord = true): bool
    {
        return $this->cache->set($entity->getCacheKey(), $this->serialize($entity));
    }

    /**
     * @param EntityInterface $entity
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function update(EntityInterface $entity, $runValidation = true, $attributeNames = null): bool
    {
        return $this->insert($entity, $runValidation, $attributeNames, false);
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function delete(EntityInterface $entity): bool
    {
        return $this->cache->delete($entity->getCacheKey());
    }

    /**
     * @param string $key
     * @return EntityInterface|null
     */
    protected function findEntityByKey(string $key): ?EntityInterface
    {
        $data = $this->cache->get($key);
        if (empty($data)) {
            return null;
        }

        return $this->hydrate((array)$data);
    }

    /**
     * @throws NotSupportedException
     */
    public function beginTransaction()
    {
        throw new NotSupportedException('Transactions are not supported in ' . static::class);
    }

    protected function serialize(EntityInterface $entity)
    {
        return array_filter($this->hydrator->extract($entity), function ($attribute) {
            return in_array($attribute, static::columns());
        }, ARRAY_FILTER_USE_KEY);
    }
}
