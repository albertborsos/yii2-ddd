<?php

namespace albertborsos\ddd\interfaces;

use yii\base\Model;
use yii\db\ActiveQueryInterface;

/**
 * Interface ActiveRepositoryInterface
 * @package albertborsos\ddd\interfaces
 * @since 1.1.0
 */
interface ActiveRepositoryInterface extends RepositoryInterface
{
    /**
     * @return ActiveQueryInterface the newly created [[ActiveQueryInterface]] instance.
     */
    public function find();

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

    /**
     * @return string
     */
    public function getDataModelClass(): string;

    /**
     * @param $className
     */
    public function setDataModelClass($className): void;
}
