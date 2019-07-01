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
    public function primaryKey()
    {
        return 'id';
    }
}
