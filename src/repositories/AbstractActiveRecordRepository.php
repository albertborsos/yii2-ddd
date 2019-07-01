<?php

namespace albertborsos\ddd\repositories;

use albertborsos\ddd\interfaces\BusinessObject;
use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\factories\EntityFactory;
use yii\base\Exception;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecordInterface;

/**
 * Class AbstractActiveRecordRepository
 * @package albertborsos\ddd\repositories
 * @since 1.1.0
 */
abstract class AbstractActiveRecordRepository extends AbstractRepository
{
    /**
     * @return string
     */
    abstract protected static function dataModelClass(): string;

    public function init()
    {
        parent::init();
        if (!\Yii::createObject(static::dataModelClass()) instanceof ActiveRecordInterface) {
            throw new Exception(get_called_class() . '::dataModelClass() must implements `yii\db\ActiveRecordInterface`');
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

    public function delete(EntityInterface $model)
    {
        /** @var ActiveRecordInterface $activerecord */
        $activerecord = \Yii::createObject(static::dataModelClass(), [$model->attributes]);

        return $activerecord->delete();
    }
}
