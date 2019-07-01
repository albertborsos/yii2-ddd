<?php

namespace albertborsos\ddd\factories;

use albertborsos\ddd\interfaces\BusinessObject;
use albertborsos\ddd\interfaces\EntityFactoryInterface;
use albertborsos\ddd\interfaces\EntityInterface;
use yii\base\Component;
use yii\db\ActiveRecordInterface;

/**
 * Class EntityFactory
 * @package albertborsos\ddd\factories
 * @since 1.1.0
 */
class EntityFactory extends Component implements EntityFactoryInterface
{
    /**
     * @param string $className
     * @param array $data
     * @return BusinessObject|mixed
     * @throws \yii\base\InvalidConfigException
     */
    public static function create(string $className, array $data)
    {
        return static::fill(\Yii::createObject($className), $data);
    }

    /**
     * @param $className
     * @param array|ActiveRecordInterface[] $models
     * @return array
     */
    public static function createCollection($className, array $models): array
    {
        return array_map(function ($model) use ($className) {
            $entity = \Yii::createObject($className);

            return static::fill($entity, $model->attributes);
        }, $models);
    }

    protected static function fill(EntityInterface $entity, array $attributes)
    {
        $attributes = static::normalizeIdAttributes($attributes);
        foreach ($attributes as $attribute => $value) {
            if (!property_exists($entity, $attribute)) {
                continue;
            }

            $entity->$attribute = $value;
        }

        return $entity;
    }

    protected static function normalizeIdAttributes(array $attributes)
    {
        if (!isset($attributes['_id'])) {
            return $attributes;
        }

        $attributes['id'] = strval($attributes['_id']);

        return $attributes;
    }
}
