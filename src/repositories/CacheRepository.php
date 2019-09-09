<?php

namespace albertborsos\ddd\repositories;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\traits\PostfixedKeyTrait;
use yii\caching\CacheInterface;
use yii\data\BaseDataProvider;
use yii\di\Instance;

/**
 * Class CacheRepository
 * Use this repository with a cache component which implements `\yii\caching\CacheInterface`
 *
 * @package albertborsos\ddd\repositories
 * @since 2.0.0
 */
class CacheRepository extends AbstractRepository
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
     * @param bool $addNotConditionForPrimaryKeys
     * @return bool
     */
    public function exists(EntityInterface $entity, $attributes = [], $addNotConditionForPrimaryKeys = false): bool
    {
        return !empty($this->findEntityByKey($entity->getCacheKey($attributes)));
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

    /**
     * @param EntityInterface $entity
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function insert(EntityInterface $entity, $runValidation = true, $attributeNames = null): bool
    {
        return $this->cache->set($entity->getCacheKey(), $entity->getDataAttributes());
    }

    /**
     * @param EntityInterface $entity
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function update(EntityInterface $entity, $runValidation = true, $attributeNames = null): bool
    {
        return $this->cache->set($entity->getCacheKey(), $entity->getDataAttributes());
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
}
