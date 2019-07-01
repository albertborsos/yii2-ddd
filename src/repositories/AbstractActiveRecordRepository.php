<?php

namespace albertborsos\ddd\repositories;

use albertborsos\ddd\factories\EntityByModelFactory;
use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\factories\EntityFactory;
use yii\base\Exception;
use yii\base\Model;
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
     * @return EntityInterface|mixed
     * @throws \yii\base\InvalidConfigException
     */
    public static function findOne($condition)
    {
        $model = call_user_func([static::dataModelClass(), 'findOne'], $condition);

        return EntityFactory::create(static::entityModelClass(), $model->attributes);
    }

    /**
     * @param $condition
     * @return EntityInterface[]|array
     */
    public static function findAll($condition)
    {
        $models = call_user_func([static::dataModelClass(), 'findAll'], $condition);

        return EntityByModelFactory::createAll(static::entityModelClass(), $models);
    }

    /**
     * @param EntityInterface|Model $model
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool|mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function save(EntityInterface $model, $runValidation = true, $attributeNames = null)
    {
        /** @var ActiveRecordInterface $activeRecord */
        $activeRecord = \Yii::createObject(static::dataModelClass(), [$model->attributes]);

        if ($activeRecord->save($runValidation, $attributeNames)) {
            return $activeRecord->getPrimaryKey();
        }

        return false;
    }

    /**
     * @param EntityInterface|Model $model
     * @return bool|int
     * @throws \yii\base\InvalidConfigException
     */
    public function delete(EntityInterface $model)
    {
        /** @var ActiveRecordInterface $activerecord */
        $activerecord = \Yii::createObject(static::dataModelClass(), [$model->attributes]);

        return $activerecord->delete();
    }
}
