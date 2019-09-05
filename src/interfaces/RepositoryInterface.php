<?php

namespace albertborsos\ddd\interfaces;

use yii\data\BaseDataProvider;

/**
 * Interface RepositoryInterface
 * @package albertborsos\ddd\interfaces
 * @since 2.0.0
 */
interface RepositoryInterface
{
    /**
     * @param $id
     * @return EntityInterface|null
     */
    public function findById($id): ?EntityInterface;

    /**
     * @param $id
     * @return EntityInterface|null
     */
    public function exists(EntityInterface $entity): bool;

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
     * @return EntityInterface
     */
    public function newEntity(): EntityInterface;

    /**
     * @param $data
     * @return EntityInterface
     */
    public function hydrate($data): EntityInterface;

    /**
     * @param $entity
     * @param $data
     * @return EntityInterface
     */
    public function hydrateInto(EntityInterface $entity, $data): EntityInterface;

    /**
     * @param $models
     * @return mixed
     */
    public function hydrateAll($models): array;

    /**
     * Creates data provider instance with search query applied
     *
     * @param $params
     * @param null $formName
     * @return BaseDataProvider
     */
    public function search($params, $formName = null): BaseDataProvider;

    /**
     * @return string
     */
    public function getEntityClass(): string;
}
