<?php

namespace albertborsos\ddd\models;

use albertborsos\ddd\interfaces\EntityInterface;
use yii\base\Model;
use yii\helpers\Inflector;

/**
 * Class AbstractEntity
 * @package albertborsos\ddd\models
 *
 * @property array $attributes
 * @property array $dataAttributes
 * @since 1.1.0
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

    public function setPrimaryKey(Model $model): void
    {
        $keys = is_array($this->getPrimaryKey()) ? $this->getPrimaryKey() : [$this->getPrimaryKey()];

        array_walk($keys, function ($key) use ($model) {
            $this->{$key} = $model->{$key};
        });
    }

    public function getCacheKey(): string
    {
        $keys = is_array($this->getPrimaryKey()) ? $this->getPrimaryKey() : [$this->getPrimaryKey()];

        $ids = array_map(function ($key) {
            return $this->{$key};
        }, $keys);

        return implode('-', array_merge([static::class], $ids));
    }

    public function getDataAttributes(): array
    {
        return array_map(function ($property) {
            return $this->{$property};
        }, $this->getPublicFieldMap());
    }

    public function fieldMapping(): array
    {
        $properties = $this->attributes();
        $dataFields = array_map(function ($propertyName) {
            return Inflector::underscore($propertyName);
        }, $properties);

        $fields = array_combine($dataFields, $properties);
        $relationFields = array_keys($this->relationMapping());

        return array_merge($fields, array_combine($relationFields, $relationFields));
    }

    /**
     * @return array|null
     */
    private function getPublicFieldMap()
    {
        $map = array_intersect_key(array_flip($this->fieldMapping()), $this->attributes);
        return array_flip($map);
    }
}
