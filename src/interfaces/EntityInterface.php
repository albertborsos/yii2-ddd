<?php

namespace albertborsos\ddd\interfaces;

use yii\base\Model;

/**
 * Interface EntityInterface
 * @package albertborsos\ddd\interfaces
 * @since 1.1.0
 */
interface EntityInterface extends BusinessObject
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
     * @param Model $model
     */
    public function setPrimaryKey(Model $model): void;

    /**
     * @return string
     */
    public function getCacheKey();

    /**
     * Mapping of keys in data array to property names.
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
     * @return array
     */
    public function getDataAttributes(): array;
}
