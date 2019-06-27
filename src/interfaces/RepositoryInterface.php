<?php

namespace albertborsos\ddd\interfaces;

use yii\db\ActiveRecordInterface;

interface RepositoryInterface
{
    public static function find();

    public static function findOne($condition);

    public static function findAll($condition);

    public static function updateAll($attributes, $condition = null);

    public static function deleteAll($condition = null);

    public function save(ActiveRecordInterface $model, $runValidation = true, $attributeNames = null);

    public function insert(ActiveRecordInterface $model, $runValidation = true, $attributeNames = null);

    public function update(ActiveRecordInterface $model, $runValidation = true, $attributeNames = null);

    public function delete(ActiveRecordInterface $model);
}
