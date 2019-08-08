<?php

namespace albertborsos\ddd\models;

use albertborsos\ddd\interfaces\EntityInterface;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\Inflector;

/**
 * Class AbstractEntity
 * @package albertborsos\ddd\models
 *
 * @property array $attributes
 * @property array $dataAttributes
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

    /**
     * Returns a unique cache key for the entity.
     *
     * @return string
     */
    public function getCacheKey(): string
    {
        $keys = is_array($this->getPrimaryKey()) ? $this->getPrimaryKey() : [$this->getPrimaryKey()];

        $ids = array_map(function ($key) {
            return $this->{$key};
        }, array_filter($keys));

        if (empty($ids)) {
            throw new InvalidConfigException('Primary key must be set for entities to generate a unique cache key.');
        }

        return implode('-', array_merge([static::class], $ids));
    }

    /**
     * Returns properties and their values which are not relation properties.
     *
     * @return array
     */
    public function getDataAttributes(): array
    {
        return array_map(function ($property) {
            return $this->{$property};
        }, $this->getDataAttributesPropertiesMap());
    }

    /**
     * Returns the data attributes and properties mapping with the relation mapping too.
     * This required to hydrate the Entity.
     *
     * @return array
     */
    public function fieldMapping(): array
    {
        $fields = $this->getDataAttributesPropertiesMap();
        $relationFields = array_keys($this->relationMapping());

        return array_merge($fields, array_combine($relationFields, $relationFields));
    }

    /**
     * Returns the data attributes of the model and the properties of the entity in key value pairs.
     * The keys are the attributes/fields/columns of the data model and the values are the properties of the entity.
     *
     * ```php
     * [
     *     'id' => 'id',
     *     'parent_id' => 'parentId',
     *     'name' => 'name',
     * ]
     * ```
     *
     * @return array|null
     */
    private function getDataAttributesPropertiesMap()
    {
        $map = array_map(function ($propertyName) {
            return Inflector::underscore($propertyName);
        }, array_combine($this->fields(), $this->fields()));

        return array_flip($map);
    }
}
