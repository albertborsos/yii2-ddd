<?php

namespace albertborsos\ddd\interfaces;

use yii\base\Model;
use yii\db\ActiveQueryInterface;

/**
 * Interface ActiveRepositoryInterface
 * @package albertborsos\ddd\interfaces
 * @since 2.0.0
 */
interface ActiveRepositoryInterface extends RepositoryInterface
{
    /**
     * @return ActiveQueryInterface the newly created [[ActiveQueryInterface]] instance.
     */
    public function find(): ActiveQueryInterface;

    /**
     * @param $condition
     * @return EntityInterface|Model
     */
    public function findOne($condition): ?EntityInterface;

    /**
     * @param $condition
     * @return array|EntityInterface[]|Model[]
     */
    public function findAll($condition): array;

    /**
     * @param EntityInterface $entity
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function save(EntityInterface $entity, $runValidation = true, $attributeNames = null): bool;

    /**
     * @param EntityInterface $entity
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function insert(EntityInterface $entity, $runValidation = true, $attributeNames = null): bool;

    /**
     * @param EntityInterface $entity
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     */
    public function update(EntityInterface $entity, $runValidation = true, $attributeNames = null): bool;

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function delete(EntityInterface $entity): bool;

    /**
     * @return string
     */
    public function getDataModelClass(): string;

    /**
     * @param $className
     */
    public function setDataModelClass($className): void;
}
