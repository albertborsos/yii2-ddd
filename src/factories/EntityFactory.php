<?php

namespace albertborsos\ddd\factories;

use albertborsos\ddd\interfaces\EntityFactoryInterface;
use albertborsos\ddd\interfaces\EntityInterface;
use yii\base\Component;
use yii\base\Exception;
use yii\base\Model;

/**
 * Class EntityFactory
 * @package albertborsos\ddd\factories
 * @since 1.1.0
 */
class EntityFactory extends Component implements EntityFactoryInterface
{
    /**
     * @param string $className
     * @param array $itemData
     * @return EntityInterface|mixed
     * @throws \yii\base\InvalidConfigException
     */
    public static function create(string $className, array $itemData)
    {
        /** @var EntityInterface $entity */
        $entity = \Yii::createObject($className);

        return static::fill($entity, $itemData);
    }

    /**
     * @param $className
     * @param array $itemsData
     * @return array
     */
    public static function createAll($className, array $itemsData): array
    {
        return array_map(function ($itemData) use ($className) {
            if ($itemData instanceof Model) {
                throw new Exception('You must use `\albertborsos\ddd\factories\EntityByModelFactory` because `' . get_class($itemData) . '` instace of `\yii\base\Model`');
            }

            return static::create($className, $itemData);
        }, $itemsData);
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
