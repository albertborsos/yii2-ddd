<?php

namespace albertborsos\ddd\repositories;

use albertborsos\ddd\interfaces\EntityInterface;
use yii\caching\CacheInterface;
use yii\data\BaseDataProvider;
use yii\di\Instance;

class CacheRepository extends AbstractCacheRepository
{
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

    public function get($key)
    {
        return $this->cache->get($key);
    }

    public function set($key, $value, $duration = null, $dependency = null)
    {
        return $this->cache->set($key, $value, $duration, $dependency);
    }

    public function delete($key)
    {
        return $this->cache->delete($key);
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
     * @return EntityInterface|null
     */
    public function findByEntity(EntityInterface $entity): ?EntityInterface
    {
        return $this->findEntityByKey($entity->getCacheKey());
    }

    /**
     * @param string $key
     * @return EntityInterface|null
     */
    public function findEntityByKey(string $key): ?EntityInterface
    {
        $data = $this->cache->get($key);
        if (empty($data)) {
            return null;
        }

        return $this->hydrate((array)$data);
    }

    /**
     * @param EntityInterface $entity
     * @param array $keyAttributes
     * @param null $duration
     * @param null $dependency
     * @return bool|mixed
     */
    public function storeEntity(EntityInterface $entity, array $keyAttributes = [], $duration = null, $dependency = null)
    {
        return $this->set($entity->getCacheKey($keyAttributes), $entity->getDataAttributes(), $duration, $dependency);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param $params
     * @param null $formName
     * @return BaseDataProvider
     */
    public function search($params, $formName = null): BaseDataProvider
    {
        // TODO: Implement search() method.
    }
}
