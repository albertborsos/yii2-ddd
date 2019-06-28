<?php

namespace albertborsos\ddd\interfaces;

interface RepositoryInterface
{
    public static function findOne($condition);

    public static function findAll($condition);

    public function save(EntityInterface $model, $runValidation = true, $attributeNames = null);
}
