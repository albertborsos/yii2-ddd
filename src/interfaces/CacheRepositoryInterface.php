<?php

namespace albertborsos\ddd\interfaces;

/**
 * Interface CacheRepositoryInterface
 * @package albertborsos\ddd\interfaces
 * @since 2.0.0
 */
interface CacheRepositoryInterface extends RepositoryInterface
{
    public function get($key);

    public function set($key, $value, $duration = null, $dependency = null);

    public function delete($key);

    /**
     * @param $id
     * @return EntityInterface|null
     */
    public function findById($id): ?EntityInterface;

    /**
     * @param EntityInterface $entity
     * @return EntityInterface|null
     */
    public function findByEntity(EntityInterface $entity): ?EntityInterface;

    /**
     * @param string $key
     * @return EntityInterface|null
     */
    public function findEntityByKey(string $key): ?EntityInterface;

    /**
     * @param EntityInterface $entity
     * @param null $duration
     * @param null $dependency
     * @return mixed
     */
    public function storeEntity(EntityInterface $entity, $duration = null, $dependency = null);
}
