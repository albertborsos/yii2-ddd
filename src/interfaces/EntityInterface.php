<?php

namespace albertborsos\ddd\interfaces;

use yii\base\Event;
use yii\base\Model;

/**
 * Interface EntityInterface
 * @package albertborsos\ddd\interfaces
 * @since 2.0.0
 */
interface EntityInterface
{
    /**
     * @event EntityEvent an event that is triggered before inserting an entity.
     * You may set [[EntityEvent::isValid]] to be `false` to stop the insertion.
     */
    const EVENT_BEFORE_INSERT = 'beforeInsert';

    /**
     * @event EntityEvent an event that is triggered before updating an entity.
     * You may set [[EntityEvent::isValid]] to be `false` to stop the update.
     */
    const EVENT_BEFORE_UPDATE = 'beforeUpdate';

    /**
     * @event \albertborsos\ddd\base\AfterSaveEvent an event that is triggered after a record is inserted.
     */
    const EVENT_AFTER_INSERT = 'afterInsert';

    /**
     * @event \albertborsos\ddd\base\AfterSaveEvent an event that is triggered after a record is updated.
     */
    const EVENT_AFTER_UPDATE = 'afterUpdate';

    /**
     * @event ModelEvent an event that is triggered before deleting a record.
     * You may set [[EntityEvent::isValid]] to be `false` to stop the deletion.
     */
    const EVENT_BEFORE_DELETE = 'beforeDelete';

    /**
     * @event Event an event that is triggered after an entity is deleted.
     */
    const EVENT_AFTER_DELETE = 'afterDelete';

    /**
     * @return string|array
     */
    public function getPrimaryKey();

    /**
     * Sets the primary key property (or properties) for the new entity from the model.
     *
     * @param Model $model
     */
    public function setPrimaryKey(Model $model): void;

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
     */
    public function getCacheKey(array $keyAttributes = [], string $postfix = null): string;

    /**
     * Triggers an event.
     * This method represents the happening of an event. It invokes
     * all attached handlers for the event including class-level handlers.
     * @param string $name the event name
     * @param Event $event the event parameter. If not set, a default [[Event]] object will be created.
     */
    public function trigger($name, Event $event = null);

    public function isNew(): bool;
}
