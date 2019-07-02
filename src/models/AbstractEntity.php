<?php

namespace albertborsos\ddd\models;

use albertborsos\ddd\interfaces\EntityInterface;
use yii\base\Model;

/**
 * Class AbstractEntity
 * @package albertborsos\ddd\models
 *
 * @property array $attributes
 * @since 1.1.0
 */
class AbstractEntity extends Model implements EntityInterface
{
    public function getPrimaryKey()
    {
        return 'id';
    }

    public function setPrimaryKey(Model $model)
    {
        $keys = is_array($this->getPrimaryKey()) ? $this->getPrimaryKey() : [$this->getPrimaryKey()];

        array_walk($keys, function ($key) use ($model) {
            $this->{$key} = $model->{$key};
        });
    }
}
