<?php

namespace albertborsos\ddd\repositories;

use albertborsos\ddd\factories\EntityByModelFactory;
use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\factories\EntityFactory;
use yii\base\Exception;
use yii\base\Model;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
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
    public function find()
    {
        return call_user_func([static::dataModelClass(), 'find']);
    }

    /**
     * @param $condition
     * @return EntityInterface|mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function findOne($condition)
    {
        $model = call_user_func([static::dataModelClass(), 'findOne'], $condition);

        return EntityFactory::create(static::entityModelClass(), $model->attributes);
    }

    /**
     * @param $condition
     * @return EntityInterface[]|array
     */
    public function findAll($condition)
    {
        $models = call_user_func([static::dataModelClass(), 'findAll'], $condition);

        return EntityByModelFactory::createAll(static::entityModelClass(), $models);
    }

    /**
     * @param EntityInterface|Model $model
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool|mixed
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function save(EntityInterface $model, $runValidation = true, $attributeNames = null)
    {
        /** @var ActiveRecord $activeRecord */
        $activeRecord = static::findOrCreate($model);

        if ($activeRecord->save($runValidation, $attributeNames)) {
            $model->setPrimaryKey($activeRecord);
            return true;
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
        /** @var ActiveRecordInterface $activeRecord */
        $activeRecord = static::findOrCreate($model);

        if ($activeRecord->delete() !== false) {
            return true;
        }

        return false;
    }

    /**
     * @param EntityInterface|Model $model
     * @return ActiveRecord
     * @throws \yii\base\InvalidConfigException
     */
    protected static function findOrCreate(EntityInterface $model)
    {
        $keys = is_array($model->getPrimaryKey()) ? $model->getPrimaryKey() : [$model->getPrimaryKey()];
        $condition = [];
        array_walk($keys, function ($key) use (&$condition, $model) {
            $condition[$key] = $model->{$key};
        });

        /** @var ActiveRecord $activeRecord */
        $activeRecord = \Yii::createObject([static::dataModelClass(), 'findOne'], [$condition]);

        if (!empty($activeRecord)) {
            $activeRecord->setAttributes($model->attributes, false);
            return $activeRecord;
        }

        return \Yii::createObject(static::dataModelClass(), [$model->attributes]);
    }
}
