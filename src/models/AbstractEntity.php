<?php

namespace albertborsos\ddd\models;

use albertborsos\ddd\interfaces\EntityInterface;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 * Class AbstractEntity
 * @package albertborsos\ddd\models
 *
 * @property array $attributes
 * @since 2.0.0
 */
abstract class AbstractEntity extends Model implements EntityInterface
{
    /**
     * @return array|string
     */
    public function getPrimaryKey()
    {
        return ['id'];
    }

    /**
     * Sets the primary key property (or properties) for the new entity from the model.
     *
     * @param Model $model
     */
    public function setPrimaryKey(Model $model): void
    {
        $keys = is_array($this->getPrimaryKey()) ? $this->getPrimaryKey() : [$this->getPrimaryKey()];
        $keys = array_filter($keys);

        array_walk($keys, function ($key) use ($model) {
            $this->{$key} = $model->{$key};
        });
    }

    public function isNew(): bool
    {
        $keys = is_array($this->getPrimaryKey()) ? $this->getPrimaryKey() : [$this->getPrimaryKey()];
        $keys = array_filter($keys);

        foreach ($keys as $key) {
            if (empty($this->{$key})) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns a unique cache key for the entity.
     *
     * You can override the key attributes. If you want to find a Page entity by it's slug attribute,
     * then you can store the entity with a different key(s) then the default key(s).
     *
     * ```php
     *     $cacheRepository->storeEntity($pageEntity, ['slug']);
     * ```
     *
     * Then if you want to find a Page by it's slug attribute, you can get a cache key based on the slug:
     *
     * ```php
     *     public function findBySlug($slug): ?PageEntity
     *     {
     *         $pageEntity = $this->hydrate(['slug' => $slug]);
     *
     *         return $this->findEntityByKey($pageEntity->getCacheKey(['slug']));
     *     }
     * ```
     *
     * You can also pass a postfix to the cache key, if you want to store a related entity.
     *
     * ```php
     *     $cacheRepository->set($pageEntity->getCacheKey([], 'next-page'), $nextPage)
     * ```
     *
     * @param string $postfix
     * @param array $keyAttributes to override default key attributes
     * @return string
     * @throws InvalidConfigException
     */
    public function getCacheKey(array $keyAttributes = [], string $postfix = null): string
    {
        if (empty($keyAttributes)) {
            $keyAttributes = is_array($this->getPrimaryKey()) ? $this->getPrimaryKey() : [$this->getPrimaryKey()];
        }

        $ids = array_map(function ($keyAttribute) {
            return $this->{$keyAttribute};
        }, array_filter($keyAttributes));

        if (empty($ids)) {
            throw new InvalidConfigException('Primary key must be set for entities to generate a unique cache key.');
        }

        $ids = array_combine($keyAttributes, $ids);

        return implode('_', array_filter(array_merge([static::class], [http_build_query($ids)], [$postfix])));
    }
}
