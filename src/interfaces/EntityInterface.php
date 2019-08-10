<?php

namespace albertborsos\ddd\interfaces;

use yii\base\Model;

/**
 * Interface EntityInterface
 * @package albertborsos\ddd\interfaces
 * @since 2.0.0
 */
interface EntityInterface
{
    /**
     * @event AfterSaveEvent an event that is triggered after an entity is saved.
     */
    const EVENT_AFTER_SAVE   = 'afterSave';

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
     * @return string
     */
    public function getCacheKey(): string;

    /**
     * Returns the data attributes and properties mapping with the relation mapping too.
     * This required to hydrate the Entity.
     *
     * @return array
     */
    public function fieldMapping(): array;

    /**
     * Mapping of property keys to entity classnames.
     *
     * @return array
     */
    public function relationMapping(): array;

    /**
     * Returns properties and their values which are not relation properties.
     *
     * @return array
     */
    public function getDataAttributes(): array;
}
