<?php

namespace albertborsos\ddd\models;

use albertborsos\ddd\interfaces\RepositoryInterface;
use yii\base\Component;
use yii\base\Exception;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecordInterface;

abstract class AbstractRepository extends Component implements RepositoryInterface
{
    abstract protected static function modelClass();

    /**
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!\Yii::createObject(static::modelClass()) instanceof ActiveRecordInterface) {
            throw new Exception(get_called_class() . '::modelClass() must implements `yii\db\ActiveRecordInterface`');
        }
    }

    /**
     * @return ActiveQueryInterface the newly created [[ActiveQueryInterface]] instance.
     */
    public static function find()
    {
        return call_user_func([static::modelClass(), 'find']);
    }

    /**
     * @param $condition
     * @return ActiveRecordInterface|null ActiveRecord instance matching the condition, or `null` if nothing matches.
     */
    public static function findOne($condition)
    {
        return call_user_func_array([static::modelClass(), 'findOne'], [$condition]);
    }

    /**
     * @param $condition
     * @return ActiveRecordInterface[]|array an array of ActiveRecord instance, or an empty array if nothing matches.
     */
    public static function findAll($condition)
    {
        return call_user_func_array([static::modelClass(), 'findAll'], [$condition]);
    }

    /**
     * @param $attributes
     * @param null $condition
     * @return int the number of rows updated
     */
    public static function updateAll($attributes, $condition = null)
    {
        return call_user_func_array([static::modelClass(), 'updateAll'], [$attributes, $condition]);
    }

    /**
     * @param null $condition
     * @return int the number of rows deleted
     */
    public static function deleteAll($condition = null)
    {
        return call_user_func_array([static::modelClass(), 'deleteAll'], [$condition]);
    }

    /**
     * @param ActiveRecordInterface $model
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool whether the saving succeeded (i.e. no validation errors occurred).
     */
    public function save(ActiveRecordInterface $model, $runValidation = true, $attributeNames = null)
    {
        return $model->save($runValidation, $attributeNames);
    }

    /**
     * @param ActiveRecordInterface $model
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool whether the attributes are valid and the record is inserted successfully.
     */
    public function insert(ActiveRecordInterface $model, $runValidation = true, $attributeNames = null)
    {
        return $model->insert($runValidation, $attributeNames);
    }

    /**
     * @param ActiveRecordInterface $model
     * @param bool $runValidation
     * @param null $attributeNames
     * @return int|bool the number of rows affected, or `false` if validation fails
     * or updating process is stopped for other reasons.
     * Note that it is possible that the number of rows affected is 0, even though the
     * update execution is successful.
     */
    public function update(ActiveRecordInterface $model, $runValidation = true, $attributeNames = null)
    {
        return $model->update($runValidation, $attributeNames);
    }

    /**
     * @param ActiveRecordInterface $model
     * @return int|bool the number of rows deleted, or `false` if the deletion is unsuccessful for some reason.
     * Note that it is possible that the number of rows deleted is 0, even though the deletion execution is successful.
     */
    public function delete(ActiveRecordInterface $model)
    {
        return $model->delete();
    }
}
