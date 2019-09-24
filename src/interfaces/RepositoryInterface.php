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
     * Mapping between the the properties of the entity and the attributes of the data model.
     * Required to hydrate, extract and serialize the entity.
     * Keys are the entity properties, values are the data attributes.
     *
     * @return array
     */
    public static function columns(): array;

    /**
     * @param $id
     * @return EntityInterface|null
     */
    public function findById($id): ?EntityInterface;

    /**
     * @param EntityInterface $entity
     * @param array $attributes
     * @param array $filter
     * @return bool
     */
    public function exists(EntityInterface $entity, $attributes = [], $filter = []): bool;

    /**
     * @param EntityInterface $entity
     * @param bool $runValidation
     * @param null $attributeNames
     * @param bool $checkIsNewRecord
     * @return bool
     */
    public function insert(EntityInterface $entity, $runValidation = true, $attributeNames = null, $checkIsNewRecord = true): bool;

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

    public function beginTransaction();
}
