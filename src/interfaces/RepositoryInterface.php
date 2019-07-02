<?php

namespace albertborsos\ddd\interfaces;

use yii\base\Model;

/**
 * Interface RepositoryInterface
 * @package albertborsos\ddd\interfaces
 * @since 1.1.0
 */
interface RepositoryInterface
{
    /**
     * @param $condition
     * @return EntityInterface|Model
     */
    public function findOne($condition);

    /**
     * @param $condition
     * @return array|EntityInterface[]|Model[]
     */
    public function findAll($condition);

    /**
     * @param EntityInterface $model
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function save(EntityInterface $model, $runValidation = true, $attributeNames = null);

    /**
     * @param EntityInterface $model
     * @return bool
     */
    public function delete(EntityInterface $model);
}
