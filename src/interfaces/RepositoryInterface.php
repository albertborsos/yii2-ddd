<?php

namespace albertborsos\ddd\interfaces;

/**
 * Interface RepositoryInterface
 * @package albertborsos\ddd\interfaces
 * @since 1.1.0
 */
interface RepositoryInterface
{
    public static function findOne($condition);

    public static function findAll($condition);

    public function save(EntityInterface $model, $runValidation = true, $attributeNames = null);

    public function delete(EntityInterface $model);
}
