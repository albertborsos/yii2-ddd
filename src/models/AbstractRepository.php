<?php

namespace albertborsos\ddd\models;

use albertborsos\ddd\interfaces\BusinessObject;
use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\interfaces\RepositoryInterface;
use yii\base\Component;
use yii\base\Exception;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecordInterface;

abstract class AbstractRepository extends Component implements RepositoryInterface
{
    /**
     * @return string
     */
    abstract protected static function businessModelClass(): string;

    /**
     * @return string
     */
    abstract protected static function dataModelClass(): string;

    /**
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!\Yii::createObject(static::dataModelClass()) instanceof ActiveRecordInterface) {
            throw new Exception(get_called_class() . '::dataModelClass() must implements `yii\db\ActiveRecordInterface`');
        }
        if (!\Yii::createObject(static::businessModelClass()) instanceof BusinessObject) {
            throw new Exception(get_called_class() . '::businessModelClass() must implements `albertborsos\ddd\interfaces\BusinessObject`');
        }
    }

    /**
     * @return ActiveQueryInterface the newly created [[ActiveQueryInterface]] instance.
     */
    protected static function find()
    {
        return call_user_func([static::dataModelClass(), 'find']);
    }

    /**
     * @param $condition
     * @return BusinessObject|mixed
     * @throws \yii\base\InvalidConfigException
     */
    public static function findOne($condition)
    {
        $model = call_user_func([static::dataModelClass(), 'findOne'], $condition);

        return EntityFactory::create(static::businessModelClass(), $model->attributes);
    }

    /**
     * @param $condition
     * @return BusinessObject[]|array
     */
    public static function findAll($condition)
    {
        $models = call_user_func([static::dataModelClass(), 'findAll'], $condition);

        return EntityFactory::createCollection(static::businessModelClass(), $models);
    }

    /**
     * @param EntityInterface $model
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool|mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function save(EntityInterface $model, $runValidation = true, $attributeNames = null)
    {
        /** @var ActiveRecordInterface $activerecord */
        $activerecord = \Yii::createObject(static::dataModelClass(), [$model->attributes]);

        if ($activerecord->save($runValidation, $attributeNames)) {
            return $activerecord->getPrimaryKey();
        }

        return false;
    }

    /**
     * @param ActiveRecordInterface $model
     */
    /**
     * @param BusinessObject $model
     * @return int|bool the number of rows deleted, or `false` if the deletion is unsuccessful for some reason.
     * Note that it is possible that the number of rows deleted is 0, even though the deletion execution is successful.
     */
    public function delete(BusinessObject $model)
    {
        return $model->delete();
    }
}
