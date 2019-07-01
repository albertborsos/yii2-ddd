<?php

namespace albertborsos\ddd\factories;

use albertborsos\ddd\interfaces\EntityInterface;
use yii\base\Exception;
use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * Class EntityFactory
 * @package albertborsos\ddd\factories
 * @since 1.1.0
 */
class EntityByModelFactory extends EntityFactory
{
    /**
     * @param $className
     * @param array|Model[] $itemsData
     * @return array|EntityInterface[]
     */
    public static function createAll($className, array $itemsData): array
    {
        return array_map(function ($model) use ($className) {
            if (!$model instanceof Model) {
                throw new Exception('`' . get_class($model) . '` must extend from `\yii\base\Model`');
            }

            /** @var ActiveRecord $model */
            return static::create($className, $model->attributes);
        }, $itemsData);
    }
}
