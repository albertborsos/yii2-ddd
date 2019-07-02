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
     * @return string|array
     */
    public function getPrimaryKey();

    public function setPrimaryKey(Model $model);
}
